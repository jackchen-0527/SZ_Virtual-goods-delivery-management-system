<template>
    <el-main>
        <!-- 搜索表单 -->
        <el-form :inline="true" :model="formsearch" class="demo-form-inline">
            <el-form-item label="需求类型">
                <el-select v-model="formsearch.type" placeholder="选择产品类型" clearable>
                    <el-option label="新需求" value="1" />
                    <el-option label="Bug修复" value="2" />
                    <el-option label="优化建议" value="3" />
                </el-select>
            </el-form-item>
            <el-form-item label="需求紧急程度">
                <el-select v-model="formsearch.type" placeholder="选择产品类型" clearable>
                    <el-option label="低" value="1" />
                    <el-option label="中" value="2" />
                    <el-option label="高" value="3" />
                    <el-option label="致命" value="4" />
                </el-select>
            </el-form-item>
            <el-form-item label="需求状态">
                <el-select v-model="formsearch.type" placeholder="选择产品类型" clearable>
                    <el-option label="待评审/未开始" value="0" />
                    <el-option label="开发中" value="1" />
                    <el-option label="待测试" value="2" />
                    <el-option label="已上线" value="3" />
                    <el-option label="已拒绝" value="4" />
                </el-select>
            </el-form-item>
        </el-form>
        <!-- 操作按钮区 -->
        <div>
            <el-button type="primary" @click="dialogFormVisible = true">
                上报新需求或问题<el-icon class="el-icon--right">
                    <Upload />
                </el-icon>
            </el-button>
        </div>
        <!-- 列表 -->
        <el-table :data="tableData" border style="width: 100%">
            <el-table-column prop="id" label="ID" />
            <el-table-column prop="name" label="需求名称" />
            <el-table-column prop="level" label="需求类型" />
            <el-table-column prop="status" label="需求紧急程度" />
            <el-table-column prop="sponsor" label="需求状态" />
            <el-table-column prop="assigned_to_user_id" label="需求发起人" />
            <el-table-column prop="handler_user_id" label="当前指派的处理人" />
            <el-table-column prop="resolved_at" label="解决时间" />
            <el-table-column prop="description" label="需求具体说明" />
            <el-table-column prop="attachments" label="附件/报错截图" />
            <el-table-column prop="reported_ip" label="上报人的IP地址" />
            <el-table-column prop="reported_device_fingerprint" label="上报人的浏览器环境指纹" />
            <el-table-column prop="created_at" label="添加时间" />
            <el-table-column prop="updated_at" label="更新时间" />
            <el-table-column label="操作" />
        </el-table>
        <!-- 分页 -->
        <el-pagination background layout="prev, pager, next" :page-size="pageSize" :total="totalUsers"
            @current-change="getDeveloperRequireTaskList" />


        <!-- 添加需求任务 弹出框 -->
        <el-dialog v-model="dialogFormVisible" title="添加需求任务" width="500">
            <el-form :model="form">
                <el-form-item label="需求名称" :label-width="formLabelWidth">
                    <el-input v-model="form.name" autocomplete="off" placeholder="请输入需求名称" />
                </el-form-item>
                <el-form-item label="需求发起人" :label-width="formLabelWidth">
                    <el-input v-model="form.sponsor" autocomplete="off" placeholder="请输入需求发起人" />
                </el-form-item>
                <el-form-item label="选择需求类型" :label-width="formLabelWidth">
                    <el-select v-model="form.type" placeholder="选择需求类型">
                        <el-option label="新需求" value="1" />
                        <el-option label="Bug修复" value="2" />
                        <el-option label="优化建议" value="3" />
                    </el-select>
                </el-form-item>
                <el-form-item label="选择需求紧急程度" :label-width="formLabelWidth">
                    <el-select v-model="form.level" placeholder="选择需求紧急程度">
                        <el-option label="低" value="1" />
                        <el-option label="中" value="2" />
                        <el-option label="高" value="3" />
                        <el-option label="致命" value="4" />
                    </el-select>
                </el-form-item>
                <el-form-item label="需求具体说明" :label-width="formLabelWidth">
                    <el-input v-model="form.description" autocomplete="off" placeholder="请输入需求具体说明" />
                </el-form-item>
                <!-- <el-upload class="upload-demo" drag :auto-upload="false" :on-change="handleChange" :limit="1" multiple>
                    <el-icon class="el-icon--upload"><upload-filled /></el-icon>
                    <div class="el-upload__text">
                        选择或拖曳文件 <em>上传</em>
                    </div>
                    <template #tip>
                        <div class="el-upload__tip">
                            jpg/png 文件大小需低于500kb
                        </div>
                    </template>
</el-upload> -->
            </el-form>
            <template #footer>
                <div class="dialog-footer">
                    <el-button @click="dialogFormVisible = false">取消</el-button>
                    <el-button type="primary" @click="submitForm">
                        确认
                    </el-button>
                </div>
            </template>
        </el-dialog>
    </el-main>
</template>

<script setup>
import { onMounted, reactive, ref } from 'vue'
import { Delete, Edit, Search, Share, Upload } from '@element-plus/icons-vue'
import { UploadFilled } from '@element-plus/icons-vue'
import axios from 'axios'
import { ElMessage } from 'element-plus'
'use strict'
const dialogTableVisible = ref(false)
const formLabelWidth = '140px'


//页面初始化
onMounted(() => {
    getDeveloperRequireTaskList()
})


//搜索技术需求任务
const formsearch = reactive({
    product_name: '',
    type: '',
    date: '',
    currentPage: 1,
    pageSize: 10,
})
const onSubmit = async () => {
    page.currentPage = 1
    isSearching.value = true
    try {
        const response = await axios.post('/admin/products/search', { params: formsearch });
        if (response.status === 200) {
            ElMessage.success('查询成功')
            tableData.value = response.data.data
            page.total = response.data.total
        } else {
            ElMessage.error("查询失败")
        }
    } catch (error) {
        console.error('获取列表出错:', error)
        ElMessage.error('获取列表失败，请检查网络')
    }
}


//获取技术需求任务列表
const isSearching = ref(false)
const tableData = ref([])
const page = reactive({
    currentPage: 1,
    pageSize: 10,
    total: 0,
})
const getDeveloperRequireTaskList = async () => {
    try {
        const response = await axios.get(`/admin/developer_task/list`, { params: page })
        if (response.status === 200 && response.data.status === 200) {
            tableData.value = response.data.data
            page.total = response.data.total
            ElMessage.success('获取技术需求任务列表成功')
        } else {
            ElMessage.error('获取技术需求任务列表失败')
        }
    } catch (error) {
        console.error('获取列表出错:', error)
        ElMessage.error('获取列表失败，请检查网络')
    }
}


//添加需求任务
const dialogFormVisible = ref(false)
const fileRaw = ref(null)
const form = reactive({
    name: '',
    type: 0,
    level: 0,
    assigned_to_user_id: 0,
    description: '',
    sponsor: '',
})

// const handleChange = (uploadFile) => {
//     fileRaw.value = uploadFile.raw
// }

const submitForm = async () => {
    // if (!fileRaw.value) {
    //     ElMessage.warning('请先选择或拖拽需要上传的压缩包')
    //     return
    // }
    try {
        const formData = new FormData();
        formData.append('name', form.name);
        formData.append('type', form.type);
        formData.append('level', form.level);
        formData.append('sponsor', form.sponsor);
        formData.append('assigned_to_user_id', form.assigned_to_user_id);
        formData.append('description', form.description);

        // formData.append('file', fileRaw.value);
        const response = await axios.post('/admin/require_issues_report/add', formData)
        if (response.status === 200) {
            ElMessage.success('提交成功')
            dialogFormVisible.value = false
            fileRaw.value = null
            getDeveloperRequireTaskList()
        } else {
            ElMessage.error(response.data.message || '提交失败')
        }
    } catch {
        console.error('请求出错:', error)
        ElMessage.error('网络错误，请稍后再试')
    }
}


const gridData = [
    {
        date: '2016-05-02',
        name: 'John Smith',
        address: 'No.1518,  Jinshajiang Road, Putuo District',
    },
    {
        date: '2016-05-04',
        name: 'John Smith',
        address: 'No.1518,  Jinshajiang Road, Putuo District',
    },
    {
        date: '2016-05-01',
        name: 'John Smith',
        address: 'No.1518,  Jinshajiang Road, Putuo District',
    },
    {
        date: '2016-05-03',
        name: 'John Smith',
        address: 'No.1518,  Jinshajiang Road, Putuo District',
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
</style>