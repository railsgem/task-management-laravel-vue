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
            <draggable tag="ul" :list="list" class="list-group" handle=".handle">

                <li
                        class="list-group-item"
                        v-for="(element, idx) in byStory"
                        :key="element.id"
                >
                    <i class="fa fa-align-justify handle"></i>

                    <span class="text" style="width: 150px;">{{ element.story_id == null ? element.absolute_day : element.min_day + '-' + element.max_day}}</span>
                    <span class="text" >{{ element.story_id == null ? 'Daily Task' : 'Story' }} </span>
                    <input style="float: right" type="text" required minlength="1" maxlength="45" size="10" class="form-control" :disabled="isInputDisabled(element.story_id)" v-model="element.story_id != null ? element.story.name: element.name" />
                </li>
            </draggable>

            <div class="button-wrap">
                <button type="button" class="btn btn-primary" v-on:click="onUpdate">Submit</button>
            </div>
        </div>

        <taskPreview class="col-4" :value="list" title="List" />
        <!--<rawDisplayer class="col-4" :value="byStory" title="List" />-->
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
                list: [
                ],
                story_min: [],
                story_max: [],
                message: null,
                errMessage: null,
                dragging: false
            };
        },
        mounted () {
            axios
                .get('api/tasks')
                .then((response)  =>  {
                    this.loading = false;
                    this.list = response.data;
                }, (error)  =>  {
                    this.loading = false;
                });
        },
        computed: {
            byStory(){
                return this.list.reduce((newTask, item) => {
                    if (item.story_id != null) {
                        (newTask['story_id:' + item.story_id ] = newTask['story_id:' + item.story_id] || []);
                        newTask['story_id:' + item.story_id] = item;
                        if (this.story_min[item.story_id] == null) {
                            this.story_min[item.story_id] = [];
                            this.story_min[item.story_id].min = item.absolute_day;
                        }
                        if (this.story_min[item.story_id].min > item.absolute_day) {
                            this.story_min[item.story_id].min = item.absolute_day;

                        }

                        if (this.story_max[item.story_id] == null) {
                            this.story_max[item.story_id] = [];
                            this.story_max[item.story_id].max = item.absolute_day;
                        }
                        if (this.story_max[item.story_id].max < item.absolute_day) {
                            this.story_max[item.story_id].max = item.absolute_day;

                        }
                        newTask['story_id:' + item.story_id].min_day = this.story_min[item.story_id].min;
                        newTask['story_id:' + item.story_id].max_day = this.story_max[item.story_id].max;
                    } else {
                        (newTask['story_id:' + 'null - item_id:' + item.id] = newTask['story_id:' + 'null - item_id:' + item.id] || []); //.push(item);
                        newTask['story_id:' + 'null - item_id:' + item.id] = item;
                    }
                    return newTask;

                }, {})
            },
            draggingInfo() {
                return this.dragging ? "under drag" : "";
            }
        },
        methods: {
            isInputDisabled(story_id) {
                if (!story_id) {
                    return false;
                }
                return story_id == null ? disabled : '';
            },
            onUpdate: function() {
                axios.put('api/tasks/updateAll', {
                    tasks: this.list
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
    .button {
        margin-top: 35px;
    }
    .handle {
        float: left;
        padding-top: 8px;
        padding-bottom: 8px;
    }
    .close {
        float: right;
        padding-top: 8px;
        padding-bottom: 8px;
    }
    #story-duration {
        width: 300px;
    }
    input {
        display: inline-block;
        width: 50%;
    }
    .text {
        margin: 20px;
    }
</style>