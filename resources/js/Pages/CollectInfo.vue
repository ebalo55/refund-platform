<script setup>
import {Head, useForm, usePage} from '@inertiajs/inertia-vue3';
import {defineProps, reactive} from "vue";
import GradientBackground from "../Components/GradientBackground";
import Header from "../Components/Header";
import GlassCard from "../Components/GlassCard";

const props = defineProps({
	first_name: String,
	last_name: String,
	update_errors: String,
	errors: Object,
})
const page = usePage()

const errors = reactive({
	msg: props.update_errors,
	visible: props.update_errors !== null,
})

const confirmation = reactive({
	msg: "You'll be redirected to the KYC in",
	remaining_seconds: 5,
	visible: false,
	interval_id: -1
})

const form = useForm({
	first_name: props.first_name,
	last_name: props.last_name,
	terms_and_conditions: false,
})

const identify = async () => {
	form.post(route("authenticated.post.collector.identify"))
}

const redirectToKYC = (url) => {
	if (confirmation.remaining_seconds > 0) {
		confirmation.remaining_seconds--
	} else {
		clearInterval(confirmation.interval_id)
		location.href = url
	}
}
</script>

<template>
	<Head title="Identification"/>
	<GradientBackground class="w-screen min-h-screen text-lg font-bold relative text-gray-900 ">
		<Header/>
		<main class="flex flex-col items-center justify-center h-full relative text-lg ">
			<GlassCard class="m-auto text-gray-900 ml-5 mr-5 p-4 md:py-14 md:px-16">
				<div class="flex flex-col justify-center items-center mb-10">
					<template v-for="(item, key) in form.errors">
						<div v-if="key && item"
						     class="flex justify-start items-center mt-2 px-4 py-2 text-xl bg-red-400 rounded-full">
							<img class="w-5 h-5" src="../../assets/error.svg">
							<span class="ml-2">{{ item }}</span>
						</div>
					</template>
					<div v-if="props.update_errors"
					     class="flex justify-start items-center mt-2 px-4 py-2 text-xl bg-red-400 rounded-full">
						<img class="w-5 h-5" src="../../assets/error.svg">
						<span class="ml-2">{{ props.update_errors }}</span>
					</div>
				</div>

				<div class="flex flex-col">
					<label class="ml-4">First name</label>
					<input type="text"
					       class="rounded-full text-b font-bold text-xl h-16 bg-white backdrop shadow-lg bg-opacity-30
                           border-0 p-4 focus:border-0 focus:ring focus:ring-violet-500 focus:ring-opacity-50"
					       placeholder="John" v-model="form.first_name">
					<small class="text-xs text-gray-600 mt-1 ml-4">
						Insert your first name as stated in your ID.
					</small>
				</div>
				<div class="flex flex-col mt-4">
					<label class="ml-4">Last name</label>
					<input type="text"
					       class="rounded-full text-b font-bold text-xl h-16 bg-white backdrop shadow-lg bg-opacity-30
                           border-0 p-4 focus:border-0 focus:ring focus:ring-violet-500 focus:ring-opacity-50"
					       placeholder="Doe" v-model="form.last_name">
					<small class="text-xs text-gray-600 ml-4 mt-1">
						Insert your last name as stated in your ID.
					</small>
				</div>

				<div class="flex items-center mt-10">
					<input
						class="h-6 w-6 focus:border-0 focus:ring focus:ring-violet-500 focus:ring-opacity-50
                        checked:bg-gray-800 cursor-pointer"
						type="checkbox" v-model="form.terms_and_conditions"
						id="terms-and-conditions">
					<label class="ml-4 cursor-pointer" for="terms-and-conditions">
						Accept
						<a href="#" class="text-blue-500 underline underline-offset-1">terms and condition</a>
						of service
					</label>
				</div>

				<div class="flex flex-col justify-center items-center">
					<button class="w-full bg-black mt-10 px-10 rounded-full flex justify-center items-center py-4 m-auto transition-all duration-500 hover:px-12
                                hover:py-6 hover:text-xl text-white"
					        @click.capture.stop="identify" :disabled="form.processing">
						<img v-if="form.processing" class="w-5 h-5 text-white animate-spin"
						     src="../../assets/spinner-svgrepo-com.svg">
						<span :class="{'ml-2':form.processing}">{{
								form.processing ? 'Processing...' : 'Proceed to KYC'
							}}</span>
					</button>
				</div>
			</GlassCard>
		</main>
	</GradientBackground>
</template>

<style scoped>
[type='checkbox'], [type='radio'] {
	color: #141414;
}
</style>
