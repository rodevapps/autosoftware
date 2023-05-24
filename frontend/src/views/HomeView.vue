<script setup lang="ts">
import { ref } from 'vue';
import { RouterLink } from 'vue-router';
import DxButton from 'devextreme-vue/button';
import { DxDataGrid, DxColumn } from 'devextreme-vue/data-grid';

const url = "http://localhost:8000/api/show-messages";
const dataGridRefKey = ref(null);
let data = [];

(async () => {
const response = await fetch(url);
const r = await response.json();

for(let v of r) {
    const date = new Date(v[2] * 1000);

    const year = date.getFullYear();
    const month = "0" + date.getMonth();
    const day = "0" + date.getDate();

    const hour = "0" + date.getHours();
    const minute = "0" + date.getMinutes();
    const second = "0" + date.getSeconds();

    const formatedDT = year + "-" + month.substr(-2) + "-" + day.substr(-2) + " " + hour.substr(-2) + ':' + minute.substr(-2) + ':' + second.substr(-2);

    if (v[1].message === undefined) {
        data.push([v[0], JSON.stringify(v[1]), formatedDT]);
    } else {
        data.push([v[0], v[1].message, formatedDT]);
    }
}

console.log(data);
dataGridRefKey.value.instance.refresh();
})();
</script>

<template>
    <div id="table">
        <RouterLink :to="{ name: 'add' }">
            <DxButton :width="170" text="Add new message" type="danger" styling-mode="contained" />
        </RouterLink>

        <div style="margin-bottom: 20px;"></div>

        <DxDataGrid id="dataGrid" :data-source="data" ref="dataGridRefKey" key-expr="0">
            <DxColumn data-field="0" data-type="string" caption="UUID" alignment="center" />
            <DxColumn data-field="1" data-type="string" caption="Message" alignment="center" />
            <DxColumn data-field="2" data-type="string" caption="Date" alignment="center" />
        </DxDataGrid>
    </div>
</template>

<style scoped>
#dataGrid {
    height: 500px;
}
</style>
