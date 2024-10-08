<template>
    <el-date-picker v-model="dateRange" type="daterange" unlink-panels range-separator="To"
        start-placeholder="Start date" end-placeholder="End date" :shortcuts="shortcuts"  />
</template>

<script  setup>
import { watch, ref } from 'vue';
import { ElDatePicker } from 'element-plus';
import 'element-plus/dist/index.css';


const dateRange = ref('')

const shortcuts = [
    {
        text: 'Last week',
        value: () => {
            const end = new Date()
            const start = new Date()
            start.setTime(start.getTime() - 3600 * 1000 * 24 * 7)
            return [start, end]
        },
    },
    {
        text: 'Last month',
        value: () => {
            const end = new Date()
            const start = new Date()
            start.setTime(start.getTime() - 3600 * 1000 * 24 * 30)
            return [start, end]
        },
    },
    {
        text: 'Last 3 months',
        value: () => {
            const end = new Date()
            const start = new Date()
            start.setTime(start.getTime() - 3600 * 1000 * 24 * 90)
            return [start, end]
        },
    },
    {
        text: 'Last 12 months',
        value: () => {
            const end = new Date()
            const start = new Date()
            start.setTime(start.getTime() - 3600 * 1000 * 24 * 365)
            return [start, end]
        },
    },
    {
        text: 'This Year',
        value: () => {
            const start = new Date(new Date().getFullYear(), 0, 1);
            const end = new Date(new Date().getFullYear(), 11, 31);
            return [start, end]
        },
    },
    {
        text: 'Last Year',
        value: () => {
            const start = new Date(new Date().getFullYear() - 1, 0, 1);
            const end = new Date(new Date().getFullYear() - 1, 11, 31);
            return [start, end]
        },
    },
]
const emit = defineEmits(['update:dateRange']);

watch(dateRange, (newValue) => {
    //console.log('Selected Date Range:', newValue);
    emit('update:dateRange', newValue);
});
</script>

<style scoped></style>
