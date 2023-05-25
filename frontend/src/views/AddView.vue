<script setup lang="ts">
import { ref } from 'vue';
import DxButton from 'devextreme-vue/button';
import { DxTextArea } from 'devextreme-vue/text-area';
import DxValidationSummary from 'devextreme-vue/validation-summary';
import { DxValidator, DxRequiredRule } from 'devextreme-vue/validator';

const message: any = ref({});

const onFormSubmit = (e: any) => {
    console.log(e.target[0].value);

    (async () => {
        const url = import.meta.env.VITE_BACKEND_URL2;

        try {
            const response = await fetch(url, {
                method: 'post',
                cache: "no-cache",
                headers: {
                    "Content-Type": "application/json",
                },
                body: JSON.stringify({"message": e.target[0].value})
            });

            let r: any = await response.text();

            const isJsonString = (str: string) => {
                try {
                    JSON.parse(str);
                } catch (e) {
                    return false;
                }
                return true;
            }

            if (isJsonString(r)) {
                r = JSON.parse(r);
            } else {
                console.error(r);
                message.value = {status: "error", body: r};
            }

            if (r.status === "success") {
                message.value = {status: "success", body: `Message added to database => ${r.uuid}!`};
            } else {
                message.value = {status: "error", body: JSON.stringify(r)};
            }

            console.log(r);

            e.target[0].value = "";
        } catch (e) {
            message.value = {status: "error", body: e};
        }
    })();
}
</script>

<template>
    <div id="form">
        <div v-if="message.body !== undefined && message.body !== ''" :class="{message: true, error: message.status === 'error', success: message.status === 'success'}">{{ message.body }}</div>
        <form @submit.prevent="onFormSubmit($event)">
            <div class="dx-fieldset">
                <div class="dx-fieldset-header">Send message</div>
                <div class="dx-field">
                    <div class="dx-field-label">Message</div>
                    <div class="dx-field-value">
                        <DxTextArea max-length="10000" :auto-resize-enabled="true">
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
.message {
    text-align: center;
    font-weight: bold;
    width: 100%;
}

.error {
    color: red;
}

.success {
    color: green;
}
</style>
