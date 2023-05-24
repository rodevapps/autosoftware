<script setup lang="ts">
import DxButton from 'devextreme-vue/button';
import { DxTextArea } from 'devextreme-vue/text-area';
import DxValidationSummary from 'devextreme-vue/validation-summary';
import { DxValidator, DxRequiredRule } from 'devextreme-vue/validator';

const onFormSubmit = (e) => {
    console.log(e.target[0].value);

    (async () => {
        const url = "http://localhost:8000/api/save-message";

        const response = await fetch(url, {
            method: 'post',
            cache: "no-cache",
            headers: {
                "Content-Type": "application/json",
            },
            body: JSON.stringify({"message": e.target[0].value})
        });

        const r = await response.json();

        console.log(r);

        e.target[0].value = "";
    })();
}
</script>

<template>
    <div id="form">
        <form @submit.prevent="onFormSubmit($event)">
            <div class="dx-fieldset">
                <div class="dx-fieldset-header">Send message</div>
                <div class="dx-field">
                    <div class="dx-field-label">Message</div>
                    <div class="dx-field-value">
                        <DxTextArea max-length="10000">
                            <DxValidator>
                                <DxRequiredRule message="Message is required"/>
                            </DxValidator>
                        </DxTextArea>
                    </div>
                </div>
            </div>
            <div class="dx-fieldset">
                <DxValidationSummary id="summary" />
                <DxButton width="100%" :use-submit-behavior="true" text="Send" type="success" />
            </div>
        </form>

        <RouterLink :to="{ name: 'home' }" style="margin-left: 20px;">
            <DxButton :width="120" text="Home Page" type="danger" styling-mode="contained" />
        </RouterLink>
    </div>
</template>

<style scoped>
</style>
