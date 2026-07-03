<template>
    <!-- 搜索表单 -->
    <el-form :inline="true" :model="formInline" class="demo-form-inline">
        <el-form-item label="Approved by">
            <el-input v-model="formInline.user" placeholder="Approved by" clearable />
        </el-form-item>
        <el-form-item label="Activity zone">
            <el-select v-model="formInline.region" placeholder="Activity zone" clearable>
                <el-option label="Zone one" value="shanghai" />
                <el-option label="Zone two" value="beijing" />
            </el-select>
        </el-form-item>
        <el-form-item label="Activity time">
            <el-date-picker v-model="formInline.date" type="date" placeholder="Pick a date" clearable />
        </el-form-item>
        <el-form-item>
            <el-button type="primary" @click="onSubmit">Query</el-button>
        </el-form-item>
    </el-form>
    <!-- 列表 -->
    <el-table :data="tableData" border style="width: 100%">
        <el-table-column prop="date" label="Date" width="180" />
        <el-table-column prop="name" label="Name" width="180" />
        <el-table-column prop="address" label="Address" />
    </el-table>
    <!-- 分页 -->
    <el-pagination background layout="prev, pager, next" :total="1000" />
    <!-- 结果弹出框 -->
    <div class="flex flex-wrap gap-1">
        <el-button class="!ml-0" plain @click="dialogVisible = true">
            Open a draggable Dialog
        </el-button>
        <el-button class="!ml-0" plain @click="dialogOverflowVisible = true">
            Open a overflow draggable Dialog
        </el-button>
        <el-button class="!ml-0" plain @click="customDraggingVisible = true">
            Open a custom dragging style Dialog
        </el-button>
    </div>

    <el-dialog v-model="dialogVisible" title="Tips" width="500" draggable>
        <span>It's a draggable Dialog</span>
        <template #footer>
            <div class="dialog-footer">
                <el-button @click="dialogVisible = false">Cancel</el-button>
                <el-button type="primary" @click="dialogVisible = false">
                    Confirm
                </el-button>
            </div>
        </template>
    </el-dialog>

    <el-dialog v-model="dialogOverflowVisible" title="Tips" width="500" draggable overflow>
        <span>It's a overflow draggable Dialog</span>
        <template #footer>
            <div class="dialog-footer">
                <el-button @click="dialogOverflowVisible = false">Cancel</el-button>
                <el-button type="primary" @click="dialogOverflowVisible = false">
                    Confirm
                </el-button>
            </div>
        </template>
    </el-dialog>

    <el-dialog v-model="customDraggingVisible" class="custom-dragging-style" title="Custom Dragging Style" width="500"
        draggable>
        <span>This dialog has custom dragging styles. Try dragging it to see the
            effects!</span>
        <template #footer>
            <div class="dialog-footer">
                <el-button @click="customDraggingVisible = false">Cancel</el-button>
                <el-button type="primary" @click="customDraggingVisible = false">
                    Confirm
                </el-button>
            </div>
        </template>
    </el-dialog>
</template>

<script setup>
import { ref } from 'vue'

const dialogVisible = ref(false)
const dialogOverflowVisible = ref(false)
const customDraggingVisible = ref(false)
import { reactive } from 'vue'

const formInline = reactive({
    user: '',
    region: '',
    date: '',
})

const onSubmit = () => {
    console.log('submit!')
}
'use strict'
const tableData = [
    {
        date: '2016-05-03',
        name: 'Tom',
        address: 'No. 189, Grove St, Los Angeles',
    },
    {
        date: '2016-05-02',
        name: 'Tom',
        address: 'No. 189, Grove St, Los Angeles',
    },
    {
        date: '2016-05-04',
        name: 'Tom',
        address: 'No. 189, Grove St, Los Angeles',
    },
    {
        date: '2016-05-01',
        name: 'Tom',
        address: 'No. 189, Grove St, Los Angeles',
    },
]
</script>

<style>
.demo-form-inline .el-input {
    --el-input-width: 220px;
}

.demo-form-inline .el-select {
    --el-select-width: 220px;
}

:global(.custom-dragging-style.is-dragging) {
    border: 2px dashed var(--el-color-primary);
    opacity: 0.65;
}
</style>