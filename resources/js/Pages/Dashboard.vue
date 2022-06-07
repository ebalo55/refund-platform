<script setup>
import {Head, useForm} from '@inertiajs/inertia-vue3';
import {defineProps, onMounted, ref} from "vue";
import GradientBackground from "../Components/GradientBackground";
import Header from "../Components/Header";
import GlassCard from "../Components/GlassCard";

const props = defineProps({
	refund_address: String,
	refund_amount: Number,
	original_address: String,
})

let pending = ref(false)
let completed = ref(false)
let queued = ref(-1)

const form = useForm({
	refund_address: props.refund_address,
	terms_and_conditions: false,
})

const checkRefundCompleted = async () => {
	try {
		const response = await axios.get(route("authenticated.get.updater.refund_completed"))
		if (response.data.data.completed) {
			pending.value = false
			completed.value = true
			clearInterval(queued.value)
		}
	} catch (e) {
		console.error(e)
	}
}

const update = async () => {
	completed.value = false
	form.post(route("authenticated.post.updater.update_address"), {
		onSuccess: params => {
			pending.value = true
			queued.value = setInterval(checkRefundCompleted, 5000)
		}
	})
}

onMounted(checkRefundCompleted)
</script>

<template>
	<Head title="Dashboard"/>
	<GradientBackground class="w-screen min-h-screen text-lg font-bold relative text-gray-900 ">
		<Header/>
		<main class="flex flex-col items-center justify-center h-full relative text-lg ">
			<GlassCard class="m-auto text-gray-900 ml-5 mr-5 p-4 md:py-14 md:px-16">

				<div class="flex flex-col justify-center items-center mb-10">
					<div v-if="completed"
					     class="flex justify-center items-center px-4 py-2 text-xl bg-green-400 rounded-full">
						<img class="w-5 h-5" src="../../assets/success.svg">
						<span class="ml-2">Refund completed successfully</span>
					</div>
					<div v-else-if="pending"
					     class="flex justify-center items-center px-4 py-2 text-xl bg-blue-400 rounded-full">
						<img class="w-5 h-5 text-white animate-spin"
						     src="../../assets/spinner-svgrepo-com.svg">
						<span class="ml-2">Waiting for refund to be confirmed, this may take a while</span>
					</div>

					<template v-for="(item, key) in form.errors">
						<div v-if="key && item"
						     class="flex justify-start items-center mt-2 px-4 py-2 text-xl bg-red-400 rounded-full">
							<img class="w-5 h-5" src="../../assets/error.svg">
							<span class="ml-2">{{ item }}</span>
						</div>
					</template>
				</div>

				<div class="flex flex-col">
					<label class="ml-4">Refund address</label>
					<input type="text"
					       class="rounded-full text-b font-bold text-xl h-16 bg-white backdrop shadow-lg bg-opacity-30
                           border-0 p-4 focus:border-0 focus:ring focus:ring-violet-500 focus:ring-opacity-50"
					       placeholder="0x000000..." v-model="form.refund_address">
					<small class="text-xs text-gray-600 ml-4 mt-1 mr-4">Originally {{ original_address }}</small>
					<small class="text-xs text-gray-600 ml-4 mr-4">
						this wallet is the only allowed to change the refund address.
					</small>
				</div>

				<div class="flex flex-col justify-center mt-10">
					<div class="flex items-end justify-between">
						<h3 class="text-lg">Refund rate:</h3>
						<span class="ml-5 text-3xl text-violet-500 font-bold">40%</span>
					</div>
					<div class="flex items-end justify-between">
						<h3 class="text-lg">You'll get back:</h3>
						<span class="ml-auto text-3xl text-violet-500 font-bold">
                            {{ refund_amount.toFixed(6) }} BNB
                        </span>
					</div>
				</div>

				<div class="flex flex-col justify-center items-center">
					<button class="w-full bg-black mt-10 px-10 rounded-full flex justify-center items-center py-4 m-auto transition-all duration-500 hover:px-12
                                hover:py-6 hover:text-xl text-white"
					        @click.capture.stop="update" :disabled="form.processing || completed || pending">
						<img v-if="form.processing" class="w-5 h-5 text-white animate-spin"
						     src="../../assets/spinner-svgrepo-com.svg">
						<span :class="{'ml-2':form.processing}">
	                        {{
								form.processing ?
									'Processing...' :
									(!completed ?
											'Request refund' :
											(pending ?
													'Refund request queued' :
													'Refund already completed'
											)
									)
							}}
                        </span>
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
