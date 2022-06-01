<template>
    <div class="backdrop bg-white bg-opacity-50 rounded-xl p-3 text-white shadow-lg">
        <slot></slot>
    </div>
</template>

<script>
export default {
    name: "GlassCard"
}
</script>

<style scoped>
.backdrop {
    backdrop-filter: blur(2px);
}
</style>
