<template>
    <table class="table table-striped">
        <thead class="thead-dark">
        <h3>Preview</h3>
        <tr>
            <th scope="col">Absolute Day</th>
            <th scope="col">Name</th>
        </tr>
        </thead>
        <draggable v-model="getItems" tag="tbody">
            <tr v-for="item in getItems" :key="item.name">
                <td scope="row">{{ item.absolute_day }}</td>
                <td>{{ item.name }}</td>
            </tr>
        </draggable>
    </table>
</template>

<script>
    const props = {
        name: "taskPreview",
        title: {
            required: true,
            type: String
        },
        value: {
            required: true
        }
    };
    export default {
        props,
        computed: {
            getItems() {
                return this.value;
            },
        }
    };
</script>