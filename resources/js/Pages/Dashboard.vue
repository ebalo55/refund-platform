<script setup>
import {Head, useForm, usePage} from '@inertiajs/inertia-vue3';
import {reactive, ref, defineProps} from "vue";

const props = defineProps({
    refund_address: String,
    refund_amount: Number,
    original_address: String,
})

let completed = ref(false)

const form = useForm({
    refund_address: props.refund_address,
    terms_and_conditions: false,
})

const update = async () => {
    form.post(route("authenticated.post.updater.update_address"), {
        onSuccess: params => {
            completed.value = true
        }
    })
}
</script>

<template>
    <Head title="Dashboard"/>

    <main class="flex flex-col items-center justify-center w-screen h-screen relative text-lg">
        <div class="m-auto border border-black px-8 py-4">
            <div class="flex flex-col">
                <label>Refund address</label>
                <input type="text" class="border-l-0 border-r-0 border-t-0 border-b border-black appearance-none" placeholder="Refund address" v-model="form.refund_address">
                <small class="text-xs">Originally {{original_address}}, this wallet is the only allowed to change the refund address.</small>
            </div>
            <div class="flex items-center mt-2">
                <input type="checkbox" v-model="form.terms_and_conditions" id="terms-and-conditions">
                <label for="terms-and-conditions">
                    Accept
                    <a href="#" class="text-blue-500">terms and condition</a>
                    of service
                </label>
            </div>

            <div class="flex items-center mt-4">
                <h3 class="mr-auto">
                    Refund rate: <span>30%</span>
                </h3>
                <h3>
                    You'll get back: <span>{{refund_amount}} BNB</span>
                </h3>
            </div>
            {{ completed }}
            {{form.errors}}

            <button class="mt-4"
                    @click.capture.stop="update" :disabled="form.processing">
                Update
            </button>
        </div>
    </main>
</template>
