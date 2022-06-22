<template>
    <header
        class="my-5 mx-auto container flex flex-col justify-center items-center transition-all duration-300 select-none backdrop
         rounded-full backdrop bg-white bg-opacity-30 rounded p-3 text-white shadow-lg">
        <nav
            class="xl:mx-10 flex items-center h-16 w-full px-6 transition-all duration-300 container mx-auto"
            :class="navAnimationClasses.nav">

            <!-- logo-->
            <a class="flex items-center w-full md:w-auto justify-start" href="/">
                <!-- desktop logo-->
                <img src="../../assets/logomeloditydark.png"
                     alt="logo" class="h-12 md:h-36 object-contain w-auto hidden md:block"
                     :class="navAnimationClasses.logo"/>
                <!-- mobile logo-->
                <img src="../../assets/logomeloditydark.png"
                     alt="logo" class="h-12 md:h-36 object-contain w-auto md:hidden"
                     :class="navAnimationClasses.logo"/>
            </a>
        </nav>
	    <h3 class="rounded-full px-4 py-2 bg-blue-400 flex items-center">
		    <img src="../../assets/info.svg" alt="info" class="h-4 object-contain mr-4 stroke-white">
		    The refund platform will be online until 07 August 2022
	    </h3>
    </header>
</template>

<script>
export default {
    name: "Header",
    data: () => ({
        scroll: {
            trigger: -1,
            animated: true,
        },
        mobile: {
            open: false,
            animate: false,
            content: false,
        },
    }),
    computed: {
        navAnimationClasses() {
            return {
                wrapper: {
                    "": this.scroll.animated,
                },
                nav: {
                    "my-4": this.scroll.animated,
                    "my-6": !this.scroll.animated,
                },
                content: {
                    "mt-64": this.scroll.animated,
                    "mt-16": !this.scroll.animated,
                },
                logo: {
                    "md:w-1/3 w-full": this.scroll.animated,
                    "md:w-2/3 w-full": !this.scroll.animated,
                },
                mobile: {
                    wrapper: {
                        "z-[-1] translate-x-full": !this.mobile.open && !this.mobile.animate,
                        "z-20": this.mobile.animate || this.mobile.open,
                        "bg-black": this.mobile.open && this.mobile.animate
                    },
                    lines: {
                        common: {
                            "translate-x-full": !this.mobile.animate
                        },
                        items: [
                            "delay-[150ms]",
                            "delay-[300ms]",
                            "delay-[450ms]",
                            "delay-[600ms]",
                            "delay-[750ms]",
                            "delay-[900ms]",
                        ]
                    },
                    content: {
                        "opacity-0": !this.mobile.content,
                        "opacity-100": this.mobile.content
                    }
                }
            }
        },
    },
}
</script>

<style scoped>
.backdrop {
    backdrop-filter: blur(2px);
}
</style>
