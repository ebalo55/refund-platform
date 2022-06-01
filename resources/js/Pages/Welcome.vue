<template>
	<GradientBackground class="w-screen h-screen text-lg font-bold relative">
		<Header/>
		<main class="flex items-center justify-center">
			<GlassCard class="flex flex-col justify-center items-center ml-5 mr-5 py-40 px-20">
				<button v-if="!confirmation.visible"
				        class="bg-black px-10 rounded-full py-4 m-auto transition-all duration-500 hover:px-12
                                hover:py-6 hover:text-xl"
				        @click.stop.prevent="connectWallet">
					Connect wallet
				</button>

				<span class="text-red-500 text-xl" v-if="errors.visible">{{ errors.msg }}</span>
			</GlassCard>
		</main>
	</GradientBackground>
</template>

<script setup>
import {reactive, ref} from "vue";
import WalletConnectProvider from "@walletconnect/web3-provider";
import Web3Modal from "web3modal";
import {ethers} from "ethers";
import GradientBackground from "../Components/GradientBackground";
import Header from "../Components/Header";
import GlassCard from "../Components/GlassCard";
import {useForm} from '@inertiajs/inertia-vue3'

const message = ref("")
const errors = reactive({
	msg: "",
	code: 0,
	visible: false,
})
const confirmation = reactive({
	msg: "Address confirmed, you'll be redirected to the KYC in",
	remaining_seconds: 5,
	visible: false,
	interval_id: -1
})

const form = useForm({
	signature: "",
	address: "",
})

const hasError = (response) => {
	const resp_array = Object.entries(response)
	if (response?.data?.status && response.data.status !== "success") {
		errors.msg = response.data.errors[0].message
		errors.code = response.data.errors[0].code
		errors.visible = true;

		return true
	}
	else if (resp_array.length > 0) {
		errors.msg = resp_array[0][1]
		errors.code = 0
		errors.visible = true;

		return true
	}
	return false
}

const getMessage = async () => {
	let response
	try {
		response = await axios.get(route("public.get.web3.message"))
		message.value = response.data.data.message
	} catch (e) {
		hasError(e.response)
	}
}

const connectWallet = async () => {
	errors.visible = false
	const provider_options = {
		walletconnect: {
			package: WalletConnectProvider, // required
			options: {
				rpc: {
					56: 'https://bsc-dataseed.binance.org',
					97: "https://data-seed-prebsc-1-s1.binance.org:8545/"
				},
				network: 'binance',
				chainId: 56,
			}
		},
		"custom-binancechainwallet": {
			display: {
				logo: "/assets/binance.png",
				name: "Binance Chain Wallet",
				description: "Connect to your Binance Chain Wallet"
			},
			package: true,
			connector: async () => {
				let provider = null;
				if (typeof window.BinanceChain !== 'undefined') {
					provider = window.BinanceChain;
					try {
						await provider.request({method: 'eth_requestAccounts'})
					} catch (error) {
						throw new Error("User Rejected");
					}
				} else {
					throw new Error("No Binance Chain Wallet found");
				}
				return provider;
			}
		},
	}

	const web3_modal = new Web3Modal({
		network: "binance",
		cacheProvider: false, // optional
		providerOptions: provider_options // required
	})

	const instance = await web3_modal.connect();
	const provider = new ethers.providers.Web3Provider(instance);
	await getMessage()
	// an error occurred while retrieving the message, stop here
	if (errors.code !== 0) {
		return
	}

	try {
		form.signature = await provider.getSigner().signMessage(message.value)
	} catch (e) {
		errors.msg = e.message
		errors.code = e.code
		errors.visible = true
		return
	}

	 form.address = await provider.getSigner().getAddress()

	form.post(route("public.post.web3.verify"), {
		onError: params => {
			console.log(params)
			hasError(params)
		}
	})
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
