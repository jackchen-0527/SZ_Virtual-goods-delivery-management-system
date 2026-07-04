<template>
    <el-main>
        <!-- 搜索表单 -->
        <el-form :inline="true" :model="formsearch" class="demo-form-inline">
            <el-form-item label="卡密密钥">
                <el-input v-model="formsearch.product_name" placeholder="输入产品名称" clearable />
            </el-form-item>
            <el-form-item label="使用者IP">
                <el-input v-model="formsearch.product_name" placeholder="输入产品名称" clearable />
            </el-form-item>
            <el-form-item label="电商平台订单ID">
                <el-input v-model="formsearch.product_name" placeholder="输入产品名称" clearable />
            </el-form-item>
            <el-form-item label="电商平台">
                <el-select v-model="formsearch.type" placeholder="选择产品类型" clearable>
                    <el-option label="exe" value="1" />
                    <el-option label="web" value="2" />
                    <el-option label="script" value="3" />
                </el-select>
            </el-form-item>
            <el-form-item label="添加时间">
                <el-date-picker v-model="formsearch.date" type="date" placeholder="选择产品添加时间" clearable />
            </el-form-item>
            <el-form-item label="卡密核销时间">
                <el-date-picker v-model="formsearch.date" type="date" placeholder="选择产品添加时间" clearable />
            </el-form-item>
            <el-form-item>
                <el-button type="primary" @click="onSubmit">搜索</el-button>
            </el-form-item>
        </el-form>
        <!-- 操作按钮区 -->
        <!-- 列表 -->
        <el-table :data="tableData" border style="width: 100%">
            <el-table-column prop="id" label="ID" />
            <el-table-column prop="product_id" label="关联产品ID" />
            <el-table-column prop="access_key" label="卡密密钥" />
            <el-table-column prop="status" label="状态" />
            <el-table-column prop="used_from_platform_name" label="来源电商平台名称" />
            <el-table-column prop="used_from_platform_order_id" label="来源电商平台订单ID" />
            <el-table-column prop="used_ip" label="使用者IP地址" />
            <el-table-column prop="used_device_fingerprint" label="使用者浏览器环境指纹" />
            <el-table-column prop="used_at" label="卡密被核销使用的时间" />
            <el-table-column prop="created_at" label="添加时间" />
            <el-table-column prop="updated_at" label="更新时间" />
            <el-table-column prop="deleted_at" label="删除时间" />
            <el-table-column label="操作" />
        </el-table>
        <!-- 分页 -->
        <el-pagination background layout="prev, pager, next" :page-size="pageSize" :total="totalUsers"
            @current-change="getProductList" />


        <!-- 添加产品弹出框 -->
        <el-dialog v-model="dialogFormVisible" title="添加产品" width="500">
            <el-form :model="form">
                <el-form-item label="产品名称" :label-width="formLabelWidth">
                    <el-input v-model="form.name" autocomplete="off" placeholder="请输入产品名称" />
                </el-form-item>
                <el-form-item label="产品路径" :label-width="formLabelWidth">
                    <el-select v-model="form.region" placeholder="请选择产品保存路径">
                        <el-option label="本地服务器对应路径" value="1" />
                        <el-option label="阿里云OSS" value="2" />
                    </el-select>
                </el-form-item>
                <el-form-item label="产品版本" :label-width="formLabelWidth">
                    <el-select v-model="form.version" placeholder="请选择产品版本">
                        <el-option label="v1.0" value="v1.0" />
                        <el-option label="v2.0" value="v2.0" />
                        <el-option label="v3.0" value="v3.0" />
                    </el-select>
                </el-form-item>
                <el-form-item label="产品类型" :label-width="formLabelWidth">
                    <el-select v-model="form.type" placeholder="请选择产品类型">
                        <el-option label="exe" value="1" />
                        <el-option label="web" value="2" />
                        <el-option label="script" value="3" />
                    </el-select>
                </el-form-item>
                <el-form-item label="解压密码" :label-width="formLabelWidth">
                    <el-input v-model="form.decompress_password" autocomplete="off" placeholder="请设置一个解压解压密码" />
                </el-form-item>
                <el-upload class="upload-demo" drag :auto-upload="false" :on-change="handleChange" :limit="1" multiple>
                    <el-icon class="el-icon--upload"><upload-filled /></el-icon>
                    <div class="el-upload__text">
                        选择或拖曳文件 <em>上传</em>
                    </div>
                    <template #tip>
                        <div class="el-upload__tip">
                            jpg/png 文件大小需低于500kb
                        </div>
                    </template>
                </el-upload>
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
    getProductList()
})


//搜索产品
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


//获取产品列表
const isSearching = ref(false)
const tableData = ref([])
const page = reactive({
    currentPage: 1,
    pageSize: 10,
    total: 0,
})
const getProductList = async () => {
    try {
        const response = await axios.get(`/stock_access_key/list`, { params: page })
        if (response.status === 200 && response.data.status === 200) {
            tableData.value = response.data.data
            page.total = response.data.total
            ElMessage.success('获取产品列表成功')
        } else {
            ElMessage.error('获取产品列表失败')
        }
    } catch (error) {
        console.error('获取列表出错:', error)
        ElMessage.error('获取列表失败，请检查网络')
    }
}


//添加产品
const dialogFormVisible = ref(false)
const fileRaw = ref(null)
const form = reactive({
    name: '',
    region: '',
    version: '',
    type: '',
    decompress_password: '',
})

const handleChange = (uploadFile) => {
    fileRaw.value = uploadFile.raw
}

const submitForm = async () => {
    if (!fileRaw.value) {
        ElMessage.warning('请先选择或拖拽需要上传的压缩包')
        return
    }
    try {
        const formData = new FormData();
        formData.append('name', form.name);
        formData.append('region', form.region);
        formData.append('version', form.version);
        formData.append('type', form.type);
        formData.append('decompress_password', form.decompress_password);
        formData.append('file', fileRaw.value);
        const response = await axios.post('/admin/products/add', formData)
        if (response.status === 200) {
            ElMessage.success('提交成功')
            dialogFormVisible.value = false
            fileRaw.value = null
            getProductList()
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