<script setup lang="ts">
import { ref } from 'vue';
import { RouterLink } from 'vue-router';
import DxButton from 'devextreme-vue/button';
import { DxPopup, DxToolbarItem } from 'devextreme-vue/popup';
import { DxDataGrid, DxColumn } from 'devextreme-vue/data-grid';

const url = import.meta.env.VITE_BACKEND_URL;

const dataGridRefKey = ref(null);
const popupVisible = ref(false);
const message = ref("");
let data: Array<Array<string>> = [];

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

if (dataGridRefKey.value !== null) {
    (dataGridRefKey.value as any).instance.refresh();
}
})();

const handleClick = (e: any) => {
  console.log(e.target.innerText);
  message.value = e.target.innerText;
  popupVisible.value = true;
}
</script>

<template>
    <div id="table">
        <RouterLink :to="{ name: 'add' }">
            <DxButton :width="170" text="Add new message" type="danger" styling-mode="contained" />
        </RouterLink>

        <div style="margin-bottom: 20px;"></div>

        <DxDataGrid id="dataGrid" :data-source="data" ref="dataGridRefKey" key-expr="0">
            <DxColumn data-field="0" data-type="string" caption="UUID" alignment="center" />
            <DxColumn data-field="1" data-type="string" caption="Message" alignment="center" cell-template="grid-cell" />
            <DxColumn data-field="2" data-type="string" caption="Date" alignment="center" />

            <template #grid-cell="{ data }">
                <span style="cursor: pointer;" v-on:click="handleClick">{{ data.value }}</span>
            </template>
        </DxDataGrid>

        <DxPopup v-model:visible="popupVisible" :drag-enabled="false" :hide-on-outside-click="true" :show-close-button="true" :show-title="true" :width="300" :height="280" container=".dx-viewport" title="Message">
          <p>{{ message }}</p>

          <DxToolbarItem widget="dxButton" toolbar="bottom" location="after" :options="{text: 'Close', onClick: () => {popupVisible = false;}}"/>
        </DxPopup>
    </div>
</template>

<style scoped>
#dataGrid {
    height: 500px;
}
</style>
