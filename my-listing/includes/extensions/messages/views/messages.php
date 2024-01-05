<?php $compose_enabled = c27()->get_setting( 'messages_enable_compose', true ) !== false; ?>
<div id="ml-messages-modal" class="modal modal-27" role="dialog">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
            <div class="sign-in-box">
                <div class="messaging-center" id="ml-message-btn">
                    <transition-group name="vopacity">
                        <compose v-if="chat.mode === 'compose'" :chat="chat" :key="'compose'"></compose>
                        <inbox v-else-if="chat.mode === 'inbox' || !chat.mode" :chat="chat" :key="'inbox'"></inbox>
                        <conversation v-else-if="chat.mode === 'conversation'" :chat="chat" :conversation="chat.conversation" :key="'conversation'"></conversation>
                    </transition-group>
                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/html" id="ml-opponent-list">
    <select name="test">
        <slot></slot>
    </select>
</script>
<script type="text/html" id="ml-compose-message">
    <div id="compose-message" class="compose-message">
        <div class="inbox-header">
            <a href="#" class="go-back-btn" @click.prevent="inbox"><i class="material-icons arrow_back"></i></a>
            <h4><?php esc_html_e('Compose', 'my-listing'); ?></h4>
            <div class="clearfix"></div>
        </div>

        <div class="compose-contents">
            <div class="select-user" v-if="!opponentId">
                <select2 :chat="chat" :options="options" v-model="selected" :url="url">
                    <option disabled value="0"><?php esc_html_e('Select one', 'my-listing'); ?></option>
                </select2>
            </div>
        </div>
    </div>
</script>

<script type="text/html" id="ml-conversation-messages">
    <ul class="messages-list">
        <li v-for="message in messages" :class="conversationClass( message )">
            <div class="chat-text">
                <span class="chat-date">{{ timestamp( message.utime ) }}</span>
                <a v-if="isPostAuthor(message)" href="#" class="avatar-img" :title="message.pdata.title">
                    <img :src="message.pdata.image" class="avatar avatar-96 photo" />
                </a>
                <a v-else href="#" class="avatar-img" v-html="senderAvatar(message)" :title="message.sender_name"></a>

                <p v-html="linkify( message )"></p>
                <a href="#" class="delete-chat" @click.stop.prevent="deleteMsg(message)" v-if="!message.loading">
                    <i class="material-icons">delete_outline</i>
                </a>
            </div>

            <div class="chat-loader" v-if="message.loading">
                <i :class="loadingClass(message)"></i>
            </div>

            <div class="delete-confirm-overlay" v-if="isDelete(message)">
                <div class="action-controllers">
                    <p class="delete-msg">
                        <?php esc_html_e('Are you sure you want to delete this message?', 'my-listing'); ?>
                    </p>
                    <a href="#" @click.stop.prevent="deleteMsg(message)">
                        <i class="material-icons check"></i> <?php esc_html_e('Yes', 'my-listing'); ?>
                    </a>
                    <a href="#" @click.stop.prevent="cancelDelete(message)">
                        <i class="material-icons close"></i> <?php esc_html_e('No', 'my-listing'); ?>
                    </a>
                </div>
            </div>
            <div class="clearfix"></div>
        </li>

        <li class="avatar-container" v-if="!isMessages">
            <?php esc_html_e( 'Say hello to', 'my-listing' ) ?>
            <span v-if="chat.postData.id">{{chat.postData.title}}
                <a :href="chat.postData.link" class="avatar-img">
                    <img :src="chat.postData.image" class="avatar avatar-96 photo" height="96" width="96" />
                </a>
            </span>

            <span v-else>{{opponent.name}} <a href="#" class="avatar-img" v-html="opponent.avatar"></a></span>
        </li>
    </ul>
</script>
<script type="text/html" id="ml-conversation">
    <div id="message-inbox-chat" class="message-inbox-chat">
        <div class="inbox-header">
            <a href="#" class="go-back-btn" @click.prevent="inbox()"><i class="material-icons arrow_back"></i></a>
            <div class="inbox-avatar">
                <span class="msg-listing-info" v-if="isPostAuthor()">
                    <div class="avatar-container">
                        <a :href="chat.postData.link" class="avatar-img">
                            <img :src="chat.postData.image" class="avatar avatar-96 photo" height="96" width="96" />
                        </a>

                        <h6><a :href="chat.postData.link">{{ chat.postData.title }}</a></h6>
                    </div>
                </span>
                <transition-group name="vopacity" v-else>
                    <div class="avatar-container" v-if="!init" :key="'loaded-convo'">
                        <a :href="opponent.uri" class="avatar-img" v-html="opponent.avatar"></a>
                        <h6><a :href="opponent.uri">{{ opponent.name }}</a></h6>
                    </div>
                </transition-group>
            </div>
            <div class="inbox-actions">
                <a href="#" class="delete-chat" @click.prevent="deleteConversation( chat.opponentId )"><i class="material-icons">delete_outline</i></a>
                <a href="#" :class="{'block-chat': true, 'active': isBlocked()}" @click.stop.prevent="blockUser()" v-if="opponent.login">
                    <i class="material-icons block" v-if="!blockRequest"></i>
                    <i class="fa fa-refresh fa-spin" v-else></i>
                </a>
            </div>
            <div class="clearfix"></div>
        </div>
        <div :class="{'inbox-chat-contents':true, '_loading': isLoading}">
            <transition name="vopacity">
                <div class="loading-more-messages" v-if="isLoading">
                    <div class="inner">
                        <i class="fa fa-refresh fa-spin"></i> <?php esc_html_e('Loading conversation', 'my-listing'); ?>
                    </div>
                </div>
            </transition>
            <messages :conversation="conversation" :opponent="opponent" :chat="chat"></messages>
            <div class="clearfix"></div>
            <form @submit.prevent="send">
                <textarea cols="30" :rows="rows" placeholder="<?php esc_html_e('Post a reply', 'my-listing'); ?>" v-model="message" :maxlength="maxLength" @keyup.enter="send($event)" :disabled="disable" id="ml-conv-textarea"></textarea>
                <button class="btn" @click.stop.prevent="send" :disabled="disable"><i class="material-icons send"></i></button>
                <span class="user-blocked" v-if="isBlocked()"><?php esc_html_e( 'You have blocked this user.', "my-listing"); ?></span>
            </form>
            <div class="clearfix"></div>
        </div>

        <div class="delete-confirm-overlay" v-if="isBlockUser()">
            <div class="action-controllers">
                <p v-if="isBlocked()">
                    <?php esc_html_e('Are you sure you want to unblock this user?', 'my-listing'); ?>
                </p>

                <p v-else>
                    <?php esc_html_e('Are you sure you want to block this user?', 'my-listing'); ?>
                </p>

                <a href="#" @click.stop.prevent="blockUser()">
                    <i class="material-icons check"></i> <?php esc_html_e('Yes', 'my-listing'); ?>
                </a>
                <a href="#" @click.stop.prevent="cancelBlock()">
                    <i class="material-icons close"></i> <?php esc_html_e('No', 'my-listing'); ?>
                </a>
            </div>
        </div>

        <div class="delete-confirm-overlay" v-if="isDelete(opponent.id)">
            <div class="action-controllers">
                <p><?php esc_html_e('Are you sure you want to delete this conversation?', 'my-listing'); ?></p>
                <a href="#" @click.stop.prevent="deleteConversation()">
                    <i class="material-icons check"></i> <?php esc_html_e('Yes', 'my-listing'); ?>
                </a>
                <a href="#" @click.stop.prevent="cancelDelete(message)">
                    <i class="material-icons close"></i> <?php esc_html_e('No', 'my-listing'); ?>
                </a>
            </div>
        </div>
    </div>
</script>

<script type="text/html" id="ml-inbox-messages">
    <ul v-if="$root.modal_open">
        <li v-for="message in messageList" @click.prevent="open(message.data)" :class="{'unread-message': !message.seen}">
            <div class="inbox-avatar">
                <a v-if="isPostAuthor(message.data)" :href="message.data.pdata.link">
                    <img :src="message.data.pdata.image" class="avatar avatar-96 photo" />
                </a>
                <a v-else :href="message.data.op.uri" v-html="message.data.op.avatar"></a>
            </div>
            <div class="message">
                <h6 v-if="isPostAuthor(message.data)">
                    <a :href="message.data.pdata.link">{{message.data.pdata.title}}</a>
                </h6>
                <h6 v-else>
                    <a :href="message.data.op.uri">{{ opponentInfo( message.data ) }}</a>
                    <span v-if="parseInt( message.data.pid )">
                        <span class="to-string"><?php esc_html_e('to', 'my-listing'); ?></span>
                        <a :href="message.data.pdata.link" class="message-listing-origin">
                            <img :src="message.data.pdata.image" class="avatar avatar-96 photo" />
                            <span>{{message.data.pdata.title}}</span>
                        </a>
                    </span>
                </h6>
                <p>{{ message.data.message }}</p>
            </div>
            <div class="date-action">
                <p class="date">{{ timestamp( message.data.utime ) }}</p>
                <a href="#" class="action" @click.stop.prevent="deleteConversation(message.data, $event)">
                    <i class="material-icons">delete_outline</i>
                </a>
            </div>

            <div class="delete-confirm-overlay" v-if="isDelete(message)">
                <div class="action-controllers">
                    <p><?php esc_html_e('Are you sure you want to delete this conversation?', 'my-listing'); ?></p>
                    <a href="#" @click.stop.prevent="deleteConversation(message.data)">
                        <i class="material-icons check"></i> <?php esc_html_e('Yes', 'my-listing'); ?>
                    </a>
                    <a href="#" @click.stop.prevent="cancelDelete(message)"><i class="material-icons close"></i> <?php esc_html_e('No', 'my-listing'); ?></a>
                </div>
            </div>
        </li>
    </ul>
</script>

<script type="text/html" id="ml-inbox">
    <div id="message-inbox" class="message-inbox">
        <div class="inbox-header">
            <h4><?php esc_html_e('Messages', 'my-listing'); ?></h4>
            <?php if ( $compose_enabled ): ?>
                <a href="#" class="compose-btn btn-primary" @click.prevent="compose"><?php esc_html_e('Compose', 'my-listing'); ?></a>
            <?php endif ?>
            <div class="clearfix"></div>
        </div>
        <div class="inbox-contents">
            <messages :chat="chat" :isLoading="isLoading" v-if="isMessages"></messages>
            <div class="inbox-contents empty-inbox" v-else>
                <p v-if="isLoading"><i class="fa fa-refresh"></i><?php esc_html_e('Loading Inbox', 'my-listing'); ?></p>
                <p v-else><?php esc_html_e('No messages available. To start a conversation, use compose button', 'my-listing'); ?></p>
                <div class="clearfix"></div>
            </div>
            <!-- Pagination -->
            <div class="load-more-msgs" v-if="loadMoreBtn && !loading" @click.prevent="loadMore">
                <?php esc_html_e( 'Load More Messages', 'my-listing' ) ?>
            </div>
            <div class="load-more-msgs" v-if="loadMoreBtn && loading">
                <i class="fa fa-circle-o-notch fa-spin"></i>
                <?php esc_html_e( 'Loading', 'my-listing' ) ?>
            </div>
        </div>
        <?php if ( $compose_enabled ): ?>
            <a href="#" class="compose-btn compose-btn-mobile btn-primary" @click.prevent="compose"><?php esc_html_e('Compose', 'my-listing'); ?></a>
        <?php endif ?>
    </div>
</script>
