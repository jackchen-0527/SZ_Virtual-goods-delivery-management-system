<template>
    <el-main class="cs-container">
        <!-- 左侧：待接入/进行中用户列表 -->
        <div class="left-panel">
            <div class="panel-header">
                <el-input v-model="searchUser" placeholder="搜索用户..." :prefix-icon="Search" clearable />
            </div>

            <el-scrollbar class="user-list-wrapper">
                <div v-for="item in filteredUserList" :key="item.id"
                    :class="['user-item', { active: currentUserId === item.id }]" @click="selectUser(item.id)">
                    <el-badge :value="item.unread" :max="99" :hidden="item.unread === 0" class="avatar-badge">
                        <el-avatar :size="40" :src="item.avatar" />
                    </el-badge>
                    <div class="user-info">
                        <div class="user-meta">
                            <span class="username">{{ item.name }}</span>
                            <span class="time">{{ item.lastTime }}</span>
                        </div>
                        <p class="last-msg">{{ item.lastMsg }}</p>
                    </div>
                </div>
            </el-scrollbar>
        </div>

        <!-- 中间：聊天主界面 -->
        <div class="center-panel">
            <!-- 聊天头部 -->
            <div class="chat-header">
                <span class="current-user-name">{{ currentChatUser?.name || '请选择用户' }}</span>
                <el-tag v-if="currentChatUser" size="small" type="success">在线</el-tag>
            </div>

            <!-- 聊天显示区域 -->
            <el-scrollbar ref="chatScrollbar" class="chat-body">
                <div v-if="!currentUserId" class="empty-chat">
                    <el-empty description="暂无新消息，请从左侧选择用户开始聊天" />
                </div>
                <div v-else class="message-list">
                    <div v-for="msg in chatMessages" :key="msg.id"
                        :class="['message-item', msg.sender === 'kf' ? 'msg-right' : 'msg-left']">
                        <el-avatar :size="36" :src="msg.sender === 'kf' ? kfAvatar : currentChatUser?.avatar" />
                        <div class="message-content">
                            <div class="msg-meta">
                                <span class="msg-time">{{ msg.time }}</span>
                            </div>
                            <div class="msg-bubble">
                                {{ msg.text }}
                            </div>
                        </div>
                    </div>
                </div>
            </el-scrollbar>

            <!-- 信息发送输入框 -->
            <div class="chat-footer">
                <!-- 工具栏 -->
                <div class="toolbar">
                    <el-button :icon="Picture" link title="发送图片" />
                    <el-button :icon="Folder" link title="发送文件" />
                    <el-button :icon="ChatDotSquare" link title="快捷回复" />
                </div>
                <!-- 输入区域 -->
                <el-input v-model="inputMsg" type="textarea" :rows="4"
                    placeholder="请输入回复内容，Ctrl + Enter 换行，Enter 键发送..." resize="none"
                    @keydown.enter.exact.prevent="handleSendMessage" />
                <div class="action-bar">
                    <el-button type="primary" :disabled="!inputMsg.trim() || !currentUserId" @click="handleSendMessage">
                        发送
                    </el-button>
                </div>
            </div>
        </div>

        <!-- 右侧：用户关联的订单信息 -->
        <div class="right-panel">
            <div class="panel-header">
                <span class="panel-title">关联客户资料</span>
            </div>

            <div v-if="!currentUserId" class="empty-right">
                <el-empty description="选择用户后查看资料" :image-size="80" />
            </div>
            <el-scrollbar v-else class="right-content">
                <!-- 用户基本信息卡片 -->
                <el-card shadow="never" class="info-card">
                    <div class="user-profile">
                        <el-avatar :size="50" :src="currentChatUser?.avatar" />
                        <span class="profile-name">{{ currentChatUser?.name }}</span>
                        <el-tag size="small" type="info">普通会员</el-tag>
                    </div>
                </el-card>

                <!-- 订单/服务标签页 -->
                <el-tabs v-model="activeTab" class="info-tabs">
                    <el-tab-pane label="关联订单" name="orders">
                        <div v-for="order in userOrders" :key="order.orderId" class="order-item-card">
                            <div class="order-title">
                                <span class="order-id">订单号：{{ order.orderId }}</span>
                                <el-tag size="small" :type="order.status === '已发货' ? 'success' : 'warning'">
                                    {{ order.status }}
                                </el-tag>
                            </div>
                            <div class="order-goods">
                                <el-image style="width: 50px; height: 50px; border-radius: 4px;" :src="order.goodsImage"
                                    fit="cover" />
                                <div class="goods-info">
                                    <p class="goods-name">{{ order.goodsName }}</p>
                                    <p class="goods-price">￥{{ order.price }}</p>
                                </div>
                            </div>
                            <div class="order-foot">
                                <span class="order-time">下单时间: {{ order.date }}</span>
                            </div>
                        </div>
                    </el-tab-pane>
                    <el-tab-pane label="历史足迹" name="footprints">
                        <el-timeline>
                            <el-timeline-item timestamp="2026-07-11 09:30" placement="top">
                                浏览了商品《智能无线蓝牙耳机》
                            </el-timeline-item>
                            <el-timeline-item timestamp="2026-07-11 08:15" placement="top">
                                咨询了退换货政策
                            </el-timeline-item>
                        </el-timeline>
                    </el-tab-pane>
                </el-tabs>
            </el-scrollbar>
        </div>
    </el-main>
</template>

<script setup>
import { onMounted, ref, computed, nextTick } from 'vue'
import { Search, Picture, Folder, ChatDotSquare } from '@element-plus/icons-vue'
import { ElMessage } from 'element-plus'

// 基础变量定义
const searchUser = ref('')
const currentUserId = ref(1)
const inputMsg = ref('')
const activeTab = ref('orders')
const chatScrollbar = ref(null)
/**
 * 核心：初始化监听公共频道
 */
const initWebSocketListener = () => {
    if (!window.Echo) {
        console.error('Laravel Echo 未初始化，请检查 bootstrap.js 配置文件');
        return;
    }

    // 监听公共频道 chat.room
    window.Echo.channel('chat.room')
        .listen('MessageSent', (e) => {
            console.log('收到 WebSocket 实时消息推送: ', e.message);

            // 收到广播后，直接把消息塞入对话流
            chatMessages.value.push({
                id: e.message.id,
                sender: e.message.sender_type === 2 ? 'kf' : 'user', // 2是客服，1是用户
                text: e.message.content,
                time: e.message.created_at
            });

            // 自动滚到底部
            scrollToBottom();
        });
}

// 客服自身头像
const kfAvatar = 'https://elemecdn.com'

// 模拟左侧用户列表数据
const userList = ref([
    { id: 1, name: '张三', avatar: 'https://elemecdn.com', lastMsg: '请问这个商品什么时候发货？', lastTime: '10:02', unread: 2 },
    { id: 2, name: '李四', avatar: 'https://elemecdn.com', lastMsg: '尺码不合适怎么退换？', lastTime: '09:55', unread: 0 },
    { id: 3, name: '王五', avatar: 'https://elemecdn.com', lastMsg: '收到了，质量很好！', lastTime: '昨天', unread: 0 }
])

// 模拟当前选中的聊天消息
const chatMessages = ref([
    { id: 1, sender: 'user', text: '你好，在吗？', time: '10:00' },
    { id: 2, sender: 'kf', text: '您好，在线的，请问有什么可以帮您？', time: '10:01' },
    { id: 3, sender: 'user', text: '请问这个商品什么时候发货？', time: '10:02' }
])

// 模拟当前选中的订单数据
const userOrders = ref([
    {
        orderId: '202607118899',
        status: '待发货',
        goodsName: '2026新款 智能运动无线蓝牙耳机降噪版',
        goodsImage: 'https://unsplash.com',
        price: '299.00',
        date: '2026-07-11 08:30'
    }
])

// 过滤用户列表
const filteredUserList = computed(() => {
    return userList.value.filter(user => user.name.includes(searchUser.value))
})

// 当前激活聊天的用户详情
const currentChatUser = computed(() => {
    return userList.value.find(user => user.id === currentUserId.value)
})

// 选择用户进行聊天
const selectUser = (id) => {
    currentUserId.value = id
    // 将未读数清零
    const user = userList.value.find(u => u.id === id)
    if (user) user.unread = 0
    scrollToBottom()
}

/**
 * 发送消息
 */
const handleSendMessage = async () => {
    if (!inputMsg.value.trim() || !currentUserId.value) return

    const textToSend = inputMsg.value
    inputMsg.value = ''

    try {
        const response = await axios.post('/admin/online_customer/send', {
            user_id: currentUserId.value,
            sender_type: 2,
            content: textToSend,
            msg_type: 'text'
        })


        if (response.data.code === 200) {

            const serverMsg = response.data.data


            chatMessages.value.push({
                id: serverMsg.id,
                sender: 'kf',
                text: serverMsg.content,
                time: serverMsg.created_at
            })

            if (currentChatUser.value) {
                currentChatUser.value.lastMsg = serverMsg.content
                currentChatUser.value.lastTime = '刚刚'
            }

            // 7. 页面平滑滚动到最底部
            scrollToBottom()
        }
    } catch (error) {
        ElMessage.error('消息发送失败，请检查网络或后端服务')
        inputMsg.value = textToSend
    }
}



// 聊天滚动条滚动到最底部
const scrollToBottom = () => {
    nextTick(() => {
        if (chatScrollbar.value) {
            chatScrollbar.value.setScrollTop(99999)
        }
    })
}

onMounted(() => {
    // 初始化默认选中第一个用户
    if (userList.value.length > 0) {
        selectUser(userList.value[0].id)
    }
    initWebSocketListener()
})
</script>

<style scoped>
.cs-container {
    display: flex;
    height: calc(100vh - 40px);
    padding: 0;
    background-color: #f5f7fa;
    gap: 12px;
    overflow: hidden;
}

/* 公共面板头部 */
.panel-header {
    padding: 15px;
    border-bottom: 1px solid #e4e7ed;
    background-color: #fff;
}

.left-panel {
    width: 280px;
    background-color: #fff;
    display: flex;
    flex-direction: column;
    border-radius: 8px;
    box-shadow: 0 2px 12px 0 rgba(0, 0, 0, 0.05);
}

.user-list-wrapper {
    flex: 1;
}

.user-item {
    display: flex;
    align-items: center;
    padding: 12px 15px;
    cursor: pointer;
    transition: all 0.2s ease;
    border-bottom: 1px solid #f2f6fc;
}

.user-item:hover {
    background-color: #f5f7fa;
}

.user-item.active {
    background-color: #ecf5ff;
}

.avatar-badge {
    margin-right: 12px;
}

.user-info {
    flex: 1;
    overflow: hidden;
}

.user-meta {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 4px;
}

.username {
    font-size: 14px;
    font-weight: 500;
    color: #303133;
}

.time {
    font-size: 12px;
    color: #909399;
}

.last-msg {
    font-size: 12px;
    color: #606266;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    margin: 0;
}

.center-panel {
    flex: 1;
    background-color: #fff;
    display: flex;
    flex-direction: column;
    border-radius: 8px;
    box-shadow: 0 2px 12px 0 rgba(0, 0, 0, 0.05);
}

.chat-header {
    height: 50px;
    padding: 0 20px;
    border-bottom: 1px solid #e4e7ed;
    display: flex;
    align-items: center;
    gap: 10px;
}

.current-user-name {
    font-weight: bold;
    font-size: 16px;
}

.chat-body {
    flex: 1;
    background-color: #f9fafc;
    padding: 20px;
}

.empty-chat {
    margin-top: 15%;
}

.message-list {
    display: flex;
    flex-direction: column;
    gap: 20px;
    padding: 10px;
}

.message-item {
    display: flex;
    gap: 12px;
    max-width: 80%;
}

.msg-left {
    align-self: flex-start;
}

.msg-right {
    align-self: flex-end;
    flex-direction: row-reverse;
}

.message-content {
    display: flex;
    flex-direction: column;
}

.msg-meta {
    font-size: 11px;
    color: #909399;
    margin-bottom: 4px;
}

.msg-right .msg-meta {
    text-align: right;
}

.msg-bubble {
    padding: 10px 14px;
    border-radius: 8px;
    font-size: 14px;
    line-height: 1.4;
    word-break: break-all;
}

.msg-left .msg-bubble {
    background-color: #fff;
    color: #303133;
    border: 1px solid #e4e7ed;
}

.msg-right .msg-bubble {
    background-color: #409eff;
    color: #fff;
}

.chat-footer {
    height: 160px;
    border-top: 1px solid #e4e7ed;
    display: flex;
    flex-direction: column;
    padding: 10px 15px;
}

.toolbar {
    display: flex;
    gap: 8px;
    margin-bottom: 5px;
}

.action-bar {
    display: flex;
    justify-content: flex-end;
    margin-top: 8px;
}

.right-panel {
    width: 320px;
    background-color: #fff;
    display: flex;
    flex-direction: column;
    border-radius: 8px;
    box-shadow: 0 2px 12px 0 rgba(0, 0, 0, 0.05);
}

.panel-title {
    font-weight: bold;
    font-size: 14px;
    color: #303133;
}

.right-content {
    flex: 1;
    padding: 15px;
}

.empty-right {
    margin-top: 30%;
}

.user-profile {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 8px;
    padding: 10px 0;
}

.profile-name {
    font-weight: bold;
    font-size: 15px;
}

.info-tabs {
    margin-top: 15px;
}

.order-item-card {
    border: 1px solid #e4e7ed;
    border-radius: 6px;
    padding: 12px;
    margin-bottom: 12px;
    background-color: #fafafa;
}

.order-title {
    display: flex;
    justify-content: space-between;
    align-items: center;
    font-size: 12px;
    margin-bottom: 10px;
}

.order-id {
    color: #909399;
}

.order-goods {
    display: flex;
    gap: 10px;
}

.goods-info {
    flex: 1;
}

.goods-name {
    font-size: 13px;
    margin: 0 0 5px 0;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
    color: #303133;
}

.goods-price {
    font-size: 13px;
    font-weight: bold;
    color: #f56c6c;
    margin: 0;
}

.order-foot {
    margin-top: 10px;
    font-size: 11px;
    color: #909399;
    text-align: right;
}
</style>