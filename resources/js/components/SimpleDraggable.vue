<template>
    <div class="row">

        <div class="col-7">
            <h3>Arrange Tasks</h3>

            <span class="text-success">{{ message }}</span>
            <span class="text-danger"><pre class="text-danger">{{ errMessage }}</pre></span>
            <div class="row">
                <div class="text"></div>
                <div class="text">Day</div>
                <div style="width: 150px" class="text" >Type</div>
                <div style="float:right" class="text">Task / Story Name</div>
            </div>
            <draggable tag="ul" :list="reducedList" class="list-group" handle=".handle" @change="updateTaskSort">

                <li
                        class="list-group-item"
                        v-for="(element, idx) in reducedList"
                        :key="element.id"
                >
                    <i class="fa fa-align-justify handle"></i>

                    <span class="text" style="width: 150px;">{{ element.story_id == null ? element.absolute_day : story_min[element.story_id].min + '-' + story_max[element.story_id].max}}</span>
                    <span class="text" >{{ element.story_id == null ? 'Daily Task' : 'Story' }} </span>
                    <input style="float: right" type="text" required minlength="1" maxlength="45" size="10" class="form-control" :disabled="isInputDisabled(element.story_id)" v-model="element.story_id != null ? element.story.name: element.name" />
                </li>
            </draggable>

            <div class="button-wrap">
                <button type="button" class="btn btn-primary" v-on:click="onSubmit">Submit</button>
            </div>
        </div>

        <taskPreview class="col-4" :value="byPreviewList" title="List" />
        <rawDisplayer class="col-4" :value="reducedList" title="List" />
    </div>
</template>

<script>
    import draggable from "vuedraggable";
    import axios from "axios";

    export default {
        name: "handle",
        display: "Handle",
        instruction: "Drag using the handle icon",
        order: 5,
        components: {
            draggable
        },
        data() {
            return {
                list: [],
                reducedList: [],
                previewList: [],
                story_min: [],
                story_max: [],
                message: null,
                errMessage: null,
                dragging: false
            };
        },
        mounted () {
            this.getTaskList();
        },
        computed: {
            byPreviewList() {
                return this.formatPreviewList();
            },
            draggingInfo() {
                return this.dragging ? "under drag" : "";
            }
        },
        methods: {
            getTaskList() {
                axios
                    .get('api/tasks')
                    .then((response)  =>  {
                        this.loading = false;
                        this.list = response.data;
                        var storySet = new Set();
                        this.list.forEach(
                            (item, index) => {
                                this.appendItemToReducedList(item, storySet);
                            }
                        );
                        this.previewList = this.formatPreviewList();
                    }, (error)  =>  {
                        this.loading = false;
                    });
            },
            appendItemToReducedList(item, storySet) {
                this.updateMinAbsoluteDay(item);
                this.updateMaxAbsoluteDay(item);
                if(item.story_id == null || !storySet.has(item.story_id)) {
                    var tempItem = item;
                    this.reducedList.push(tempItem);
                    storySet.add(item.story_id);
                }
                if (item.story_id != null) {
                    const index = this.list.findIndex(element => element.id === item.id);
                    var subtask = { task_id: this.list[index].id, absolute_day: this.list[index].absolute_day,  task_name: this.list[index].name};
                    const storyIndex = this.reducedList.findIndex(element => element.story_id === item.story_id);
                    if (this.reducedList[storyIndex].sub_tasks == undefined) {
                        this.reducedList[storyIndex].sub_tasks = [];
                    }
                    this.reducedList[storyIndex].sub_tasks.push(subtask);
                }
            },
            updateMinAbsoluteDay(item) {
                if (this.story_min[item.story_id] == null) {
                    this.story_min[item.story_id] = [];
                    this.story_min[item.story_id].min = item.absolute_day;
                }
                if (this.story_min[item.story_id].min > item.absolute_day) {
                    this.story_min[item.story_id].min = item.absolute_day;
                }
            },
            updateMaxAbsoluteDay(item) {
                if (this.story_max[item.story_id] == null) {
                    this.story_max[item.story_id] = [];
                    this.story_max[item.story_id].max = item.absolute_day;
                }
                if (this.story_max[item.story_id].max < item.absolute_day) {
                    this.story_max[item.story_id].max = item.absolute_day;

                }
            },
            formatPreviewList() {
                this.previewList = [];
                this.reducedList.forEach((item, index) => {
                    if (item.story_id != null) {
                        if (item.story.daily_tasks_lisk !== undefined) {
                            var tasks = item.sub_tasks;
                            tasks.forEach(
                                (daily_task, index) => {
                                    var tempDailyTask = {
                                        'id': daily_task.task_id,
                                        'absolute_day': daily_task.absolute_day,
                                        'name': daily_task.task_name,
                                        'order': item.order,
                                    };
                                    this.previewList.push(tempDailyTask);
                                }
                            );
                        }
                    } else {
                        var tempItem = {
                            'id': item.id,
                            'absolute_day': item.absolute_day,
                            'name': item.name,
                            'order': item.order,
                        };
                        this.previewList.push(tempItem);
                    }
                });
                return this.previewList;
            },
            isInputDisabled(story_id) {
                if (!story_id) {
                    return false;
                }
                return story_id == null ? disabled : '';
            },
            updateTaskSort() {
                this.reducedList.map((task, index) => {
                    task.order = index + 1;
                });
            },
            onSubmit: function() {
                console.log(this.previewList);
                axios.put('api/tasks/updateAll', {
                    tasks: this.previewList
                }).then((response) => {
                    this.errMessage = '';
                    this.message = response.data;
                    console.log(response.data);
                }).catch((error) => {
                    this.message = '';
                    if (error.response.status == 422){
                        this.errMessage = error.response.data.errors;
                    }
                    console.log(error);
                })
            },
        }
    };
</script>
<style scoped>
    .button-wrap {
        text-align: center;
        margin: 20px;
    }
    .handle {
        float: left;
        padding-top: 8px;
        padding-bottom: 8px;
    }
    input {
        display: inline-block;
        width: 50%;
    }
    .text {
        margin: 20px;
    }
</style>