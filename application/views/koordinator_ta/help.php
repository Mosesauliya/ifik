<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title><?= htmlspecialchars($title ?? 'Bantuan & Live Chat - Koordinator TA') ?></title>
    
    <!-- Google Fonts & FontAwesome & SweetAlert2 -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <style>
        :root {
            --bg-color: #f8fafc;
            --surface-color: #ffffff;
            --text-color: #0f172a;
            --text-muted: #64748b;
            --border-color: #e2e8f0;
            --primary: #ea580c;
            --primary-hover: #c2410c;
            --primary-light: #fff7ed;
            --laboran-color: #ea580c;
            --laboran-bg: #fff7ed;
            --laboran-border: #fed7aa;
            --kaur-color: #4f46e5;
            --kaur-bg: #eef2ff;
            --kaur-border: #c7d2fe;
            --admin-color: #9333ea;
            --admin-bg: #faf5ff;
            --admin-border: #e9d5ff;
            --radius-md: 12px;
            --radius-lg: 16px;
            --radius-xl: 20px;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        html, body {
            height: 100%;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: var(--bg-color);
            color: var(--text-color);
            height: 100vh;
            overflow: hidden;
        }

        .page-wrapper-for-sidebar {
            width: 100%;
            height: 100vh;
            padding: 16px 24px;
            display: flex;
            flex-direction: column;
            overflow: hidden;
            box-sizing: border-box;
            transition: margin-left 0.75s cubic-bezier(0.76, 0, 0.24, 1), width 0.75s cubic-bezier(0.76, 0, 0.24, 1), padding-left 0.75s cubic-bezier(0.76, 0, 0.24, 1);
        }

        @media (min-width: 1024px) {
            .page-wrapper-for-sidebar {
                margin-left: 270px;
                width: calc(100% - 270px);
                padding-left: 24px;
            }

            body.curved-sidebar-desktop-collapsed .page-wrapper-for-sidebar {
                margin-left: 0;
                width: 100%;
                padding-left: 76px;
            }
        }

        @media (max-width: 1023.98px) {
            .page-wrapper-for-sidebar {
                margin-left: 0 !important;
                width: 100% !important;
                padding: 56px 12px 12px 12px;
                height: 100dvh;
                height: 100vh;
            }
        }

        .main-container {
            max-width: 1440px;
            width: 100%;
            margin: 0 auto;
            height: 100%;
            display: flex;
            flex-direction: column;
            overflow: hidden;
            min-height: 0;
            flex: 1;
        }

        /* Top Page Header */
        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 10px;
            margin-bottom: 12px;
            flex-shrink: 0;
        }

        .header-title-wrap h1 {
            font-size: 1.35rem;
            font-weight: 800;
            color: #0f172a;
            letter-spacing: -0.02em;
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            gap: 8px 10px;
        }

        .badge-service-hub {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 3px 12px;
            background: #fff7ed;
            color: #ea580c;
            border: 1px solid #fed7aa;
            border-radius: 99px;
            font-size: 0.72rem;
            font-weight: 700;
        }

        /* Chat Workspace */
        .chat-workspace {
            display: grid;
            grid-template-columns: 340px 1fr;
            gap: 14px;
            flex: 1;
            min-height: 0;
            overflow: hidden;
        }

        /* Left Contacts Panel */
        .chat-sidebar {
            background: #ffffff;
            border: 1.5px solid var(--border-color);
            border-radius: var(--radius-lg);
            display: flex;
            flex-direction: column;
            overflow: hidden;
            box-shadow: 0 4px 16px rgba(15, 23, 42, 0.03);
        }

        .chat-sidebar-header {
            padding: 14px 16px;
            border-bottom: 1px solid var(--border-color);
            display: flex;
            flex-direction: column;
            gap: 4px;
            background: #ffffff;
            flex-shrink: 0;
        }

        .chat-sidebar-header h3 {
            font-size: 0.88rem;
            font-weight: 800;
            color: #1e293b;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .chat-sidebar-header p {
            font-size: 0.73rem;
            color: #64748b;
        }

        .contacts-list {
            flex: 1;
            overflow-y: auto;
            padding: 10px;
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .contact-channel-card {
            padding: 12px 14px;
            border-radius: 14px;
            border: 1.5px solid var(--border-color);
            background: #ffffff;
            cursor: pointer;
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
            gap: 12px;
            position: relative;
        }

        .contact-channel-card:hover {
            border-color: #fdba74;
            background: #f8fafc;
            transform: translateY(-1px);
        }

        .contact-channel-card.active {
            background: #fff7ed !important;
            border-color: #ea580c !important;
            box-shadow: 0 0 0 2px rgba(234, 88, 12, 0.15) !important;
        }

        .contact-avatar {
            width: 44px;
            height: 44px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.35rem;
            flex-shrink: 0;
            position: relative;
        }

        .contact-channel-card.laboran .contact-avatar {
            background: var(--laboran-bg);
            border: 1px solid var(--laboran-border);
        }

        .contact-channel-card.kaur .contact-avatar {
            background: var(--kaur-bg);
            border: 1px solid var(--kaur-border);
        }

        .contact-channel-card.admin_layanan .contact-avatar {
            background: var(--admin-bg);
            border: 1px solid var(--admin-border);
        }

        .online-dot {
            position: absolute;
            bottom: -2px;
            right: -2px;
            width: 10px;
            height: 10px;
            border-radius: 50%;
            background: #22c55e;
            border: 2px solid #fff;
        }

        .contact-info {
            flex: 1;
            min-width: 0;
        }

        .contact-name {
            font-size: 0.86rem;
            font-weight: 800;
            color: #0f172a;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 6px;
        }

        .contact-desc {
            font-size: 0.72rem;
            color: #64748b;
            margin-top: 2px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .contact-unread-badge {
            background: #ea580c;
            color: #fff;
            font-size: 0.68rem;
            font-weight: 800;
            padding: 1px 7px;
            border-radius: 99px;
            flex-shrink: 0;
        }

        /* Right Area: Main Active Direct Chat Room */
        .chat-main-area {
            background: #ffffff;
            border: 1.5px solid var(--border-color);
            border-radius: var(--radius-lg);
            display: flex;
            flex-direction: column;
            overflow: hidden;
            box-shadow: 0 4px 16px rgba(15, 23, 42, 0.03);
            position: relative;
        }

        /* Header Chat Area */
        .chat-header {
            padding: 12px 18px;
            border-bottom: 1px solid var(--border-color);
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            background: #ffffff;
            flex-shrink: 0;
            z-index: 10;
        }

        .chat-header-user {
            display: flex;
            align-items: center;
            gap: 12px;
            min-width: 0;
            flex: 1;
        }

        .btn-mobile-back {
            display: none;
            background: #f1f5f9;
            border: 1px solid #e2e8f0;
            color: #0f172a;
            font-size: 0.95rem;
            cursor: pointer;
            width: 34px;
            height: 34px;
            border-radius: 10px;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            transition: background 0.15s;
        }

        .btn-mobile-back:active {
            background: #e2e8f0;
        }

        .header-avatar {
            width: 44px;
            height: 44px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 800;
            font-size: 1.05rem;
            color: #ffffff;
            background: linear-gradient(135deg, #ea580c, #f97316);
            flex-shrink: 0;
        }

        .header-info {
            min-width: 0;
            flex: 1;
        }

        .header-info h2 {
            font-size: 1.02rem;
            font-weight: 800;
            color: #0f172a;
            display: flex;
            align-items: center;
            gap: 8px;
            min-width: 0;
        }

        .header-info h2 span:first-child {
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            min-width: 0;
        }

        .role-pill {
            font-size: 0.64rem;
            font-weight: 800;
            padding: 2px 7px;
            border-radius: 6px;
            letter-spacing: 0.02em;
            flex-shrink: 0;
        }

        .role-pill.laboran {
            background: #ffedd5;
            color: #ea580c;
        }

        .role-pill.kaur {
            background: #e0e7ff;
            color: #4f46e5;
        }

        .role-pill.admin_layanan {
            background: #fae8ff;
            color: #9333ea;
        }

        .header-info p {
            font-size: 0.78rem;
            color: #64748b;
            margin-top: 2px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            min-width: 0;
        }

        .header-actions-wrap {
            display: flex;
            align-items: center;
            gap: 8px;
            flex-shrink: 0;
        }

        .btn-header-refresh {
            width: 36px;
            height: 36px;
            border-radius: 10px;
            border: 1px solid var(--border-color);
            background: #f8fafc;
            color: #64748b;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.15s;
            flex-shrink: 0;
        }

        .btn-header-refresh:hover {
            background: #fff7ed;
            color: #ea580c;
            border-color: #fdba74;
        }

        /* Message Feed Area */
        .chat-messages {
            flex: 1;
            min-height: 0;
            overflow-y: auto;
            padding: 18px 20px;
            background: #f8fafc;
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        /* System Notice Pill */
        .system-message-divider {
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 6px 0;
            width: 100%;
            animation: bubbleFadeIn 0.15s ease;
        }

        .system-message-divider span {
            background: #f1f5f9;
            color: #64748b;
            font-size: 0.72rem;
            font-weight: 600;
            padding: 4px 14px;
            border-radius: 999px;
            border: 1px solid #e2e8f0;
            text-align: center;
            max-width: 90%;
            line-height: 1.4;
            box-shadow: 0 1px 2px rgba(0,0,0,0.02);
        }

        /* Message Bubbles - Identical to Laboran Panel */
        .message-row {
            display: flex;
            align-items: flex-end;
            gap: 10px;
            max-width: 80%;
            animation: bubbleFadeIn 0.15s ease;
        }

        @keyframes bubbleFadeIn {
            from { opacity: 0; transform: translateY(4px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .message-row.incoming {
            align-self: flex-start;
        }

        .message-row.outgoing {
            align-self: flex-end;
            flex-direction: row-reverse;
        }

        .bubble-avatar {
            width: 32px;
            height: 32px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.75rem;
            font-weight: 800;
            color: #ffffff;
            flex-shrink: 0;
            margin-bottom: 2px;
        }

        .bubble-avatar.user-av { background: #0284c7; }
        .bubble-avatar.koor-av { background: #0284c7; }
        .bubble-avatar.laboran-av { background: #ea580c; }
        .bubble-avatar.kaur-av { background: #4f46e5; }
        .bubble-avatar.admin_layanan-av { background: #9333ea; }

        .message-bubble {
            padding: 12px 16px;
            border-radius: 18px;
            font-size: 0.88rem;
            line-height: 1.45;
            position: relative;
            box-shadow: 0 2px 5px rgba(0,0,0,0.03);
            word-break: break-word;
        }

        .message-row.incoming .message-bubble {
            background: #ffffff;
            color: #0f172a;
            border: 1px solid #e2e8f0;
            border-bottom-left-radius: 4px;
        }

        .message-row.outgoing .message-bubble {
            background: linear-gradient(135deg, #ea580c, #f97316);
            color: #ffffff;
            border-bottom-right-radius: 4px;
            box-shadow: 0 4px 12px rgba(234, 88, 12, 0.2);
        }

        .sender-tag {
            font-size: 0.72rem;
            font-weight: 700;
            margin-bottom: 4px;
            display: block;
        }

        .message-row.incoming .sender-tag { color: #ea580c; }
        .message-row.outgoing .sender-tag { color: #fed7aa; }

        .message-meta {
            display: flex;
            align-items: center;
            justify-content: flex-end;
            gap: 4px;
            font-size: 0.68rem;
            margin-top: 4px;
            font-weight: 600;
        }

        .message-row.incoming .message-meta { color: #94a3b8; }
        .message-row.outgoing .message-meta { color: #fed7aa; }

        /* Input Area - Identical to Laboran Panel */
        .chat-input-area {
            padding: 12px 18px;
            background: #ffffff;
            border-top: 1px solid #e2e8f0;
            display: flex;
            align-items: flex-end;
            gap: 10px;
            flex-shrink: 0;
        }

        .chat-textarea {
            flex: 1;
            min-height: 42px;
            height: 42px;
            max-height: 120px;
            padding: 9px 14px;
            border: 1px solid #cbd5e1;
            border-radius: 12px;
            outline: none;
            font-family: inherit;
            font-size: 0.86rem;
            resize: none;
            background: #f8fafc;
            transition: border-color 0.2s, box-shadow 0.2s;
            line-height: 1.4;
            box-sizing: border-box;
            overflow-y: hidden;
        }

        .chat-textarea:focus {
            border-color: #ea580c;
            background: #ffffff;
            box-shadow: 0 0 0 3px rgba(234, 88, 12, 0.12);
        }

        .btn-send {
            width: 42px;
            height: 42px;
            border-radius: 12px;
            background: linear-gradient(135deg, #ea580c, #f97316);
            color: #ffffff;
            border: none;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.05rem;
            cursor: pointer;
            transition: all 0.2s;
            box-shadow: 0 4px 12px rgba(234, 88, 12, 0.3);
            flex-shrink: 0;
        }

        .btn-send:hover {
            transform: scale(1.05);
            background: linear-gradient(135deg, #c2410c, #ea580c);
        }

        .btn-send:active {
            transform: scale(0.95);
        }

        /* Fullscreen Mobile View & Responsive Layout */
        @media (max-width: 900px) {
            body {
                padding: 0;
                overflow: hidden;
            }

            .chat-workspace {
                grid-template-columns: 1fr;
                position: relative;
                gap: 0;
            }

            .chat-workspace.mobile-active .chat-sidebar {
                display: none;
            }

            .chat-workspace:not(.mobile-active) .chat-main-area {
                display: none;
            }

            /* Fullscreen mobile active chat state */
            body.mobile-chat-open .page-wrapper-for-sidebar {
                padding: 0 !important;
                height: 100dvh !important;
                height: 100vh !important;
            }

            body.mobile-chat-open .page-header {
                display: none !important;
            }

            body.mobile-chat-open #curvedSidebarToggle,
            body.mobile-chat-open .curved-sidebar-btn,
            body.mobile-chat-open .floating-sidebar-toggle {
                display: none !important;
            }

            body.mobile-chat-open .chat-workspace {
                height: 100dvh !important;
                height: 100vh !important;
                border-radius: 0 !important;
                border: none !important;
                display: flex !important;
                flex-direction: column !important;
                overflow: hidden !important;
            }

            body.mobile-chat-open .chat-main-area {
                border-radius: 0 !important;
                border: none !important;
                height: 100% !important;
                min-height: 0 !important;
                display: flex !important;
                flex-direction: column !important;
                flex: 1 !important;
                width: 100% !important;
                overflow: hidden !important;
            }

            body.mobile-chat-open .chat-messages {
                flex: 1 !important;
                min-height: 0 !important;
                overflow-y: auto !important;
                display: flex !important;
                flex-direction: column !important;
            }

            /* Active Chat Header on Mobile */
            .chat-header {
                padding: 10px 12px;
                gap: 8px;
            }

            .chat-header-user {
                gap: 8px;
                min-width: 0;
                flex: 1;
                display: flex;
                align-items: center;
            }

            .btn-mobile-back {
                display: inline-flex;
                width: 32px;
                height: 32px;
                border-radius: 8px;
                flex-shrink: 0;
                background: #f1f5f9;
                color: #334155;
                font-size: 0.88rem;
                align-items: center;
                justify-content: center;
                border: 1px solid #e2e8f0;
            }

            .header-avatar {
                width: 36px;
                height: 36px;
                border-radius: 10px;
                font-size: 0.85rem;
                font-weight: 800;
                flex-shrink: 0;
            }

            .header-info {
                min-width: 0;
                flex: 1;
                display: flex;
                flex-direction: column;
                justify-content: center;
            }

            .header-info h2 {
                font-size: 0.88rem;
                font-weight: 700;
                gap: 6px;
                display: flex;
                align-items: center;
                min-width: 0;
                line-height: 1.25;
            }

            .header-info h2 span:first-child {
                white-space: nowrap;
                overflow: hidden;
                text-overflow: ellipsis;
                min-width: 0;
                flex-shrink: 1;
            }

            .header-info .role-pill {
                font-size: 0.58rem;
                padding: 1px 5px;
                border-radius: 4px;
                letter-spacing: 0.02em;
                flex-shrink: 0;
            }

            .header-info p {
                font-size: 0.72rem;
                color: #64748b;
                margin-top: 2px;
                white-space: nowrap;
                overflow: hidden;
                text-overflow: ellipsis;
                min-width: 0;
                line-height: 1.2;
            }

            /* Message Feed Area on Mobile */
            .chat-messages {
                padding: 12px 10px;
                gap: 8px;
            }

            .message-row {
                max-width: 88%;
                gap: 6px;
            }

            .bubble-avatar {
                width: 28px;
                height: 28px;
                font-size: 0.7rem;
                border-radius: 8px;
            }

            .message-bubble {
                padding: 8px 11px;
                border-radius: 13px;
                font-size: 0.82rem;
            }

            /* Chat Input on Mobile */
            .chat-input-area {
                padding: 10px 12px;
                gap: 8px;
                background: #ffffff;
                border-top: 1px solid #e2e8f0;
                padding-bottom: max(10px, env(safe-area-inset-bottom));
                align-items: flex-end;
            }

            .chat-textarea {
                min-height: 42px;
                height: 42px;
                max-height: 120px;
                padding: 9px 12px;
                font-size: 0.86rem;
                border-radius: 12px;
                line-height: 1.4;
                box-sizing: border-box;
                overflow-y: hidden;
            }

            .btn-send {
                width: 42px;
                height: 42px;
                border-radius: 12px;
                font-size: 1rem;
            }
        }
    </style>
</head>
<body>

    <!-- Load Curved Animated Sidebar Component -->
    <?php $this->load->view('components/curved_sidebar'); ?>

    <div class="page-wrapper-for-sidebar">
        <div class="main-container">

            <!-- Top Page Header -->
            <div class="page-header">
                <div class="header-title-wrap">
                    <h1>
                        💬 Bantuan & Live Chat
                        <span class="badge-service-hub">
                            <i class="fa-solid fa-bolt"></i> Obrolan Langsung Petugas Layanan
                        </span>
                    </h1>
                </div>
            </div>

            <!-- Direct Chat Workspace -->
            <div class="chat-workspace" id="chatWorkspace">
                
                <!-- Left Sidebar: 3 Direct Service Channels -->
                <div class="chat-sidebar">
                    <div class="chat-sidebar-header">
                        <h3><i class="fa-solid fa-comments text-orange-600"></i> Saluran Bantuan Langsung</h3>
                        <p>Pilih petugas tujuan dan langsung kirimkan pesan Anda.</p>
                    </div>

                    <!-- Channels List -->
                    <div class="contacts-list">
                        <!-- 1. Laboran -->
                        <div class="contact-channel-card laboran active" id="channel_laboran" onclick="switchChannel('laboran')">
                            <div class="contact-avatar">
                                🧪
                                <div class="online-dot"></div>
                            </div>
                            <div class="contact-info">
                                <div class="contact-name">
                                    <span>Petugas Laboran</span>
                                    <span class="contact-unread-badge" id="unread_laboran" style="display: none;">0</span>
                                </div>
                                <div class="contact-desc">Fasilitas Lab, Alat, Jaringan & Ruang Uji</div>
                            </div>
                        </div>

                        <!-- 2. Ka. Ur -->
                        <div class="contact-channel-card kaur" id="channel_kaur" onclick="switchChannel('kaur')">
                            <div class="contact-avatar">
                                🏛️
                                <div class="online-dot"></div>
                            </div>
                            <div class="contact-info">
                                <div class="contact-name">
                                    <span>Ka. Ur Laboratorium</span>
                                    <span class="contact-unread-badge" id="unread_kaur" style="display: none;">0</span>
                                </div>
                                <div class="contact-desc">Kebijakan Lab, Validasi & Izin Khusus</div>
                            </div>
                        </div>

                        <!-- 3. Admin Layanan -->
                        <div class="contact-channel-card admin_layanan" id="channel_admin_layanan" onclick="switchChannel('admin_layanan')">
                            <div class="contact-avatar">
                                📋
                                <div class="online-dot"></div>
                            </div>
                            <div class="contact-info">
                                <div class="contact-name">
                                    <span>Admin Layanan (LAA)</span>
                                    <span class="contact-unread-badge" id="unread_admin_layanan" style="display: none;">0</span>
                                </div>
                                <div class="contact-desc">Berkas TA, SK Pembimbing & Yudisium</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Main Area: Active Direct Chat Room -->
                <div class="chat-main-area">
                    
                    <!-- Top Chat Header -->
                    <div class="chat-header">
                        <div class="chat-header-user">
                            <button type="button" class="btn-mobile-back" onclick="closeMobileChat()">
                                <i class="fa-solid fa-arrow-left"></i>
                            </button>
                            <div class="header-avatar" id="headerAvatar">LB</div>
                            <div class="header-info">
                                <h2>
                                    <span id="activeTargetName">Petugas Laboran</span>
                                    <span class="role-pill laboran" id="activeTargetRolePill">LABORAN</span>
                                </h2>
                                <p id="activeTargetSub">Fasilitas Lab, Ruang & Alat Pengujian</p>
                            </div>
                        </div>

                        <div class="header-actions-wrap">
                            <button type="button" class="btn-header-refresh" onclick="refreshActiveChannel(true)" title="Segarkan Pesan">
                                <i class="fa-solid fa-rotate"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Message Feed -->
                    <div class="chat-messages" id="messagesContainer">
                        <div style="padding: 40px 10px; text-align: center; color: #94a3b8; font-size: 0.8rem;">
                            <i class="fa-solid fa-circle-notch fa-spin"></i> Memuat percakapan...
                        </div>
                    </div>

                    <!-- Bottom Direct Input Area -->
                    <div class="chat-input-area">
                        <textarea id="composerInput" class="chat-textarea" placeholder="Tulis balasan pesan..." rows="1" onkeydown="handleComposerKey(event)"></textarea>
                        <button type="button" class="btn-send" id="btnSendChat" onclick="sendDirectMessage()" title="Kirim Balasan">
                            <i class="fa-solid fa-paper-plane"></i>
                        </button>
                    </div>

                </div>

            </div>

        </div>
    </div>

    <!-- Scripts -->
    <script>
        let currentTargetRole = 'laboran';
        let activeConversationId = null;
        let pollingTimer = null;
        let renderedConversationId = null;
        let renderedMessageIds = new Set();

        const TARGET_CONFIG = {
            'laboran': {
                name: 'Petugas Laboran',
                roleBadge: 'LABORAN',
                roleBadgeClass: 'laboran',
                sub: 'Fasilitas Lab, Ruang & Alat Pengujian',
                emoji: '🧪',
                avatarInit: 'LB',
                avatarBg: 'linear-gradient(135deg, #ea580c, #f97316)',
                avatarClass: 'laboran-av',
                tagColor: '#ea580c',
                placeholder: 'Tulis balasan pesan...'
            },
            'kaur': {
                name: 'Ka. Ur Laboratorium',
                roleBadge: 'KA. UR',
                roleBadgeClass: 'kaur',
                sub: 'Kebijakan Lab, Validasi & Izin Khusus',
                emoji: '🏛️',
                avatarInit: 'KU',
                avatarBg: 'linear-gradient(135deg, #4f46e5, #6366f1)',
                avatarClass: 'kaur-av',
                tagColor: '#4f46e5',
                placeholder: 'Tulis balasan pesan...'
            },
            'admin_layanan': {
                name: 'Admin Layanan (LAA)',
                roleBadge: 'ADMIN LAYANAN',
                roleBadgeClass: 'admin_layanan',
                sub: 'Berkas TA, SK Pembimbing & Yudisium',
                emoji: '📋',
                avatarInit: 'AL',
                avatarBg: 'linear-gradient(135deg, #9333ea, #a855f7)',
                avatarClass: 'admin_layanan-av',
                tagColor: '#9333ea',
                placeholder: 'Tulis balasan pesan...'
            }
        };

        $(document).ready(function() {
            // Load initial channel
            switchChannel('laboran', false);

            // Real-time polling every 3.5 seconds
            pollingTimer = setInterval(function() {
                refreshActiveChannel(false);
            }, 3500);

            // Auto resize textarea
            const textarea = document.getElementById('composerInput');
            if (textarea) {
                textarea.addEventListener('input', function() {
                    this.style.height = '42px';
                    const newHeight = Math.min(Math.max(this.scrollHeight, 42), 120);
                    this.style.height = newHeight + 'px';
                    this.style.overflowY = this.scrollHeight > 120 ? 'auto' : 'hidden';
                });
            }
        });

        function isFeedAtBottom() {
            const feed = document.getElementById('messagesContainer');
            if (!feed) return true;
            return (feed.scrollHeight - feed.scrollTop - feed.clientHeight) < 80;
        }

        // Switch active channel
        function switchChannel(targetRole, showLoader = true) {
            if (currentTargetRole !== targetRole) {
                renderedConversationId = null;
                renderedMessageIds.clear();
            }

            currentTargetRole = targetRole;

            $('.contact-channel-card').removeClass('active');
            $('#channel_' + targetRole).addClass('active');

            const conf = TARGET_CONFIG[targetRole] || TARGET_CONFIG['laboran'];
            $('#headerAvatar').text(conf.avatarInit).css('background', conf.avatarBg);
            $('#activeTargetName').text(conf.name);
            
            const badgeEl = $('#activeTargetRolePill');
            badgeEl.text(conf.roleBadge).attr('class', 'role-pill ' + conf.roleBadgeClass);

            $('#activeTargetSub').text(conf.sub);
            $('#composerInput').attr('placeholder', conf.placeholder);

            // On mobile, show fullscreen chat area
            if (window.innerWidth <= 900) {
                $('body').addClass('mobile-chat-open');
                $('#chatWorkspace').addClass('mobile-active');
            }

            loadChannelMessages(targetRole, showLoader);
        }

        // Fetch messages for active channel
        function loadChannelMessages(targetRole, showLoader = true) {
            if (showLoader && renderedConversationId === null) {
                $('#messagesContainer').html(`
                    <div style="padding: 40px 10px; text-align: center; color: #94a3b8; font-size: 0.8rem;">
                        <i class="fa-solid fa-circle-notch fa-spin"></i> Memuat percakapan...
                    </div>
                `);
            }

            $.ajax({
                url: '<?= site_url("koordinatorta/help/channel") ?>',
                type: 'GET',
                data: { target: targetRole },
                dataType: 'json',
                success: function(res) {
                    if (res && res.status === 'success') {
                        activeConversationId = res.conversation.id;
                        renderMessages(res.messages, showLoader);
                        updateUnreadBadges(res.unreads);
                    } else {
                        renderMessages([], showLoader);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error fetching channel messages:', error, xhr.responseText);
                    $('#messagesContainer').html(`
                        <div style="padding: 40px 10px; text-align: center; color: #ef4444; font-size: 0.82rem;">
                            <i class="fa-solid fa-triangle-exclamation" style="font-size: 1.5rem; margin-bottom: 8px;"></i>
                            <div style="font-weight: 700;">Gagal memuat percakapan</div>
                            <div style="font-size: 0.75rem; color: #64748b; margin-top: 4px;">Koneksi terputus atau server sedang sibuk.</div>
                            <button type="button" onclick="loadChannelMessages('${targetRole}', true)" style="margin-top: 10px; padding: 4px 14px; background: #ea580c; color: #fff; border: none; border-radius: 6px; font-size: 0.75rem; cursor: pointer;">Coba Lagi</button>
                        </div>
                    `);
                }
            });
        }

        function refreshActiveChannel(scrollToBottom = false) {
            if (!currentTargetRole) return;
            $.ajax({
                url: '<?= site_url("koordinatorta/help/channel") ?>',
                type: 'GET',
                data: { target: currentTargetRole },
                dataType: 'json',
                success: function(res) {
                    if (res && res.status === 'success') {
                        activeConversationId = res.conversation.id;
                        renderMessages(res.messages, scrollToBottom);
                        updateUnreadBadges(res.unreads);
                    }
                },
                error: function(xhr, status, error) {
                    console.warn('Silent refresh error:', error);
                }
            });
        }

        function updateUnreadBadges(unreads) {
            if (!unreads) return;
            ['laboran', 'kaur', 'admin_layanan'].forEach(k => {
                const count = unreads[k] || 0;
                const el = $('#unread_' + k);
                if (count > 0 && k !== currentTargetRole) {
                    el.text(count).show();
                } else {
                    el.hide();
                }
            });
        }

        function escapeHtml(text) {
            if (!text) return '';
            const map = { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' };
            return text.toString().replace(/[&<>"']/g, function(m) { return map[m]; });
        }

        function getInitials(name) {
            if (!name) return 'DK';
            const parts = name.trim().split(/\s+/);
            if (parts.length === 1) return parts[0].substring(0, 2).toUpperCase();
            return (parts[0][0] + parts[parts.length - 1][0]).toUpperCase();
        }

        function isFeedAtBottom() {
            const feed = document.getElementById('messagesContainer');
            if (!feed) return true;
            return (feed.scrollHeight - feed.scrollTop - feed.clientHeight) < 80;
        }

        function renderMessages(messages, scrollToBottom = false) {
            const msgBox = $('#messagesContainer');
            const conf = TARGET_CONFIG[currentTargetRole] || TARGET_CONFIG['laboran'];
            const isDifferentConv = (renderedConversationId !== activeConversationId);

            if (isDifferentConv) {
                renderedConversationId = activeConversationId;
                renderedMessageIds.clear();
                msgBox.empty();
            }

            if (!messages || messages.length === 0) {
                if (isDifferentConv) {
                    msgBox.html(`
                        <div style="padding: 50px 14px; text-align: center; color: #94a3b8;">
                            <div style="font-size: 2.5rem; margin-bottom: 8px;">${conf.emoji}</div>
                            <div style="font-size: 0.9rem; font-weight: 800; color: #334155;">Mulai Chat Langsung dengan ${conf.name}</div>
                            <div style="font-size: 0.76rem; color: #64748b; margin-top: 4px; max-width: 320px; margin-left: auto; margin-right: auto;">
                                Ketik pertanyaan atau kebutuhan bantuan Anda pada kotak pesan di bawah. Petugas akan langsung merespons.
                            </div>
                        </div>
                    `);
                }
                return;
            }

            // Remove empty placeholder if any
            if (msgBox.find('> div:not(.message-row)').length > 0) {
                msgBox.empty();
            }

            const wasAtBottom = isFeedAtBottom();
            let hasNew = false;

            messages.forEach(m => {
                if (renderedMessageIds.has(m.id)) {
                    return; // Already rendered in DOM, skip to prevent flickering
                }

                hasNew = true;
                renderedMessageIds.add(m.id);

                const isMe = m.is_me;
                const rowClass = isMe ? 'outgoing' : 'incoming';
                const avatarClass = isMe ? 'koor-av' : conf.avatarClass;
                const avatarInit = isMe ? 'DK' : conf.avatarInit;
                const checkMark = isMe ? '<i class="fa-solid fa-check-double text-orange-200" style="font-size: 0.65rem;"></i>' : '';
                const senderTag = isMe ? 'Anda (Koordinator TA)' : (m.sender_name || conf.name);

                const html = `
                    <div class="message-row ${rowClass}" data-msg-id="${m.id}">
                        <div class="bubble-avatar ${avatarClass}">${avatarInit}</div>
                        <div class="message-bubble">
                            <span class="sender-tag">${escapeHtml(senderTag)}</span>
                            <div>${m.message}</div>
                            <div class="message-meta">
                                <span>${m.time}</span>
                                ${checkMark}
                            </div>
                        </div>
                    </div>
                `;
                msgBox.append(html);
            });

            if (hasNew && (scrollToBottom || wasAtBottom || isDifferentConv)) {
                msgBox.scrollTop(msgBox[0].scrollHeight);
            }
        }

        // Send Direct Message
        function sendDirectMessage() {
            const input = $('#composerInput');
            const message = input.val().trim();

            if (!message) return;

            if (!activeConversationId) {
                // If no active conv id yet, create conversation directly
                $.ajax({
                    url: '<?= site_url("koordinatorta/help/create") ?>',
                    type: 'POST',
                    data: {
                        target_role: currentTargetRole,
                        topik: TARGET_CONFIG[currentTargetRole].name,
                        message: message
                    },
                    dataType: 'json',
                    success: function(res) {
                        input.val('');
                        if (res.status === 'success') {
                            refreshActiveChannel(true);
                        }
                    }
                });
                return;
            }

            input.val('');
            const textarea = document.getElementById('composerInput');
            if (textarea) textarea.style.height = '42px';

            $.ajax({
                url: '<?= site_url("koordinatorta/help/send") ?>',
                type: 'POST',
                data: {
                    conversation_id: activeConversationId,
                    message: message
                },
                dataType: 'json',
                success: function(res) {
                    if (res.status === 'success') {
                        refreshActiveChannel(true);
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal',
                            text: res.message || 'Terjadi kesalahan.',
                            confirmButtonColor: '#ea580c'
                        });
                    }
                }
            });
        }

        function handleComposerKey(e) {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                sendDirectMessage();
            }
        }

        function closeMobileChat() {
            $('body').removeClass('mobile-chat-open');
            $('#chatWorkspace').removeClass('mobile-active');
        }
    </script>
</body>
</html>
