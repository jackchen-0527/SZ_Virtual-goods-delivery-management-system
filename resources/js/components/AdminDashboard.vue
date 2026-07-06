<template>
  <el-row class="tac" style="height: 100vh; background-color: #f5f7fa;">
    <el-col :span="4" style="background-color: #304156; height: 100%;">
      <div style="padding: 20px; text-align: center; color: #fff; font-weight: bold; background-color: #2b3643;">
        森泽软件发货管理系统
      </div>
      <el-menu :default-active="activeMenuSlug" background-color="#304156" text-color="#bfcbd9"
        active-text-color="#409EFF" style="border: none;" @select="handleMenuSelect" v-for="menu in menuList"
        :key="menu.id" :index="menu.slug">

        <!-- 子菜单栏 -->
        <el-sub-menu v-if="menu.children && menu.children.length > 0" :index="menu.slug">
          <template #title>
            <el-icon>
              <component :is="getIconComponent(menu.icon)" />
            </el-icon>
            <span>{{ menu.name }}</span>
          </template>
          <el-menu-item v-for="child in menu.children" :key="child.id" :index="child.slug">
            <span>{{ child.name }}</span></el-menu-item>
        </el-sub-menu>

        <el-menu-item v-else :index="menu.slug">
          <el-icon>
            <component :is="getIconComponent(menu.icon)" />
          </el-icon>
          <span>{{ menu.name }}</span>
        </el-menu-item>


      </el-menu>
    </el-col>

    <el-col :span="20" style="padding: 20px; overflow-y: auto; height: 100%;">
      <div
        style="background: #fff; padding: 15px; margin-bottom: 20px; box-shadow: 0 1px 4px rgba(0,21,41,.08); display: flex; align-items: center; justify-content: space-between;">
        <span style="font-weight: bold; font-size: 16px; color: #303133;">
          当前位置：{{ currentMenuName }}
        </span>
      </div>

      <div v-if="activeMenuSlug === 'menu_manager'">
        <el-row :gutter="20">
          <!-- 录入卡片 -->
          <el-col :span="8">
            <el-card shadow="hover" header="新增菜单项">
              <el-form label-position="top">
                <el-form-item label="菜单名称">
                  <el-input v-model="form.name" placeholder="如:产品管理" />
                </el-form-item>
                <el-form-item label="选用图标">
                  <el-select v-model="form.icon" placeholder="请选择图标" style="width: 100%;">
                    <el-option label="指南针 (Compass)" value="Compass" />
                    <el-option label="卡片链接 (Link)" value="Link" />
                    <el-option label="设置 (Setting)" value="Setting" />
                  </el-select>
                </el-form-item>
                <el-form-item label="选择上级,如果没有则不选">
                  <el-select v-model="form.parent_id" placeholder="请选择上级" style="width: 100%;">
                    <el-option v-for="menu in menuList" :key="menu.id" :label=menu.name :value=menu.id />
                  </el-select>
                </el-form-item>
                <el-form-item label="菜单标识">
                  <el-input v-model="form.slug" placeholder="输入英文，如：product_panel" />
                </el-form-item>
                <el-button type="primary" style="width: 100%;" :loading="submitLoading"
                  @click="submitToDatabase">保存并写入MySQL</el-button>
              </el-form>
            </el-card>
          </el-col>

          <!-- 数据明细表 -->
          <el-col :span="16">
            <el-card shadow="hover" header="菜单项列表">
              <el-table :data="menuList" style="width: 100%" border stripe v-loading="tableLoading">
                <el-table-column prop="id" label="ID" width="120" align="center" />
                <el-table-column prop="name" label="菜单名称" />
                <el-table-column label="渲染图标" width="120" align="center">
                  <template #default="scope">
                    <el-icon :size="18">
                      <component :is="getIconComponent(scope.row.icon)" />
                    </el-icon>
                  </template>
                </el-table-column>
                <el-table-column prop="slug" label="唯一标识 (Slug)" />
              </el-table>
            </el-card>
          </el-col>
        </el-row>
      </div>

      <!-- 发货管理 -->
      <div v-if="activeMenuSlug === 'shipment_manager'">
        <ShipmentManager />
      </div>

      <!-- 产品管理 -->
      <div v-if="activeMenuSlug === 'products_manager'">
        <ProductsManager />
      </div>

      <!-- 库存卡密管理 -->
      <div v-if="activeMenuSlug === 'stock_accesskey_manager'">
        <StockAccessKey />
      </div>

      <!-- 需求问题与上报 -->
      <div v-if="activeMenuSlug === 'require_issues_resport'">
        <RequireIssuesReport />
      </div>

      <!-- 开发者任务列表 -->
      <div v-if="activeMenuSlug === 'developer_task_list'">
        <RequireIssuesReport />
      </div>

      <!-- 销售员任务列表 -->
      <div v-if="activeMenuSlug === 'salesperson_task_list'">
        <RequireIssuesReport />
      </div>
    </el-col>

  </el-row>
</template>

<script setup>
import {
  Document,
  Menu as IconMenu,
  Location,
  Setting,
  Compass,
  Link as LinkIcon,
} from '@element-plus/icons-vue'
import { ElMessage } from 'element-plus'
import axios from 'axios'
import { ref, computed, onMounted } from 'vue'
import ShipmentManager from '@/components/ShipmentManager.vue'
import ProductsManager from '@/components/ProductsManager.vue'
import StockAccessKey from './StockAccessKey.vue'
import RequireIssuesReport from './RequireIssuesReport.vue'
const menuList = ref([])

const activeMenuSlug = ref('menu_manager') // 默认死锁在菜单管理

const tableLoading = ref(false)
const submitLoading = ref(false)

const form = ref({ name: '', icon: 'Compass', slug: '' })

// 2. 页面加载完毕时的「自动初始化请求机制」
const fetchMenus = async () => {
  tableLoading.value = true
  try {
    const res = await axios.get('/api/menus')
    menuList.value = res.data
  } catch (error) {
    ElMessage.error('无法从本地MySQL捞取菜单，请检查网络或数据库连接')
  } finally {
    tableLoading.value = false
  }
}

onMounted(() => {
  fetchMenus() // 原地爆破执行请求
})

// 3. 提交新菜单写入本地数据库
const submitToDatabase = async () => {
  if (!form.value.name || !form.value.slug) {
    ElMessage.warning('请确保信息填写完整')
    return
  }
  submitLoading.value = true
  try {
    const res = await axios.post('/api/menus/store', form.value)
    if (res.data.success) {
      ElMessage.success('🎉 写入 MySQL 数据库成功！菜单已动态追加。')
      form.value = { name: '', icon: 'Compass', slug: '' } // 擦除表单
      await fetchMenus() // 核心：重新向数据库拉一次最新数据，刷新左侧菜单
    }
  } catch (err) {
    ElMessage.error(err.response?.data?.message || '存储失败，可能存在重复的唯一标识Slug')
  } finally {
    submitLoading.value = false
  }
}

// 动态名称计算
const currentMenuName = computed(() => {
  const matched = menuList.value.find(m => m.slug === activeMenuSlug.value)
  return matched ? matched.name : '系统菜单管理'
})

const handleMenuSelect = (slug) => {
  activeMenuSlug.value = slug
}

const getIconComponent = (name) => {
  const dict = { 'Compass': Compass, 'Menu': IconMenu, 'Link': LinkIcon, 'Setting': Setting }
  return dict[name] || Location
}

</script>