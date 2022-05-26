<script setup>
import {Head} from '@inertiajs/inertia-vue3';
import {reactive, ref, defineProps} from "vue";

defineProps({
    refund_address: String,
    refund_amount: Number,
    original_address: String,
})
</script>

<template>
    <Head title="Melodity Refund | Dashboard"/>

    <main class="flex flex-col items-center justify-center w-screen h-screen relative text-lg">
        <div class="m-auto border border-black px-8 py-4">
            {{refund_address}}
            {{refund_amount}}
            {{original_address}}
        </div>
    </main>
</template>
