<style>
    /* Стили для sticky элементов */
    #messageInputContainer {
        backdrop-filter: blur(8px);
        -webkit-backdrop-filter: blur(8px);
    }
    
    #chatHeader {
        backdrop-filter: blur(8px);
        -webkit-backdrop-filter: blur(8px);
    }
    
    /* Стили для Select2 */
    .select2-container--default .select2-selection--single {
        height: 42px;
        border: 1px solid #d1d5db;
        border-radius: 0.5rem;
        background-color: white;
    }
    
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 40px;
        padding-left: 12px;
        color: #374151;
    }
    
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 40px;
        right: 8px;
    }
    
    .select2-dropdown {
        border: 1px solid #d1d5db;
        border-radius: 0.5rem;
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
    }
    
    .select2-container--default .select2-results__option--highlighted[aria-selected] {
        background-color: #3b82f6;
    }
    
    /* Темная тема для Select2 */
    .dark .select2-container--default .select2-selection--single {
        background-color: #374151;
        border-color: #4b5563;
    }
    
    .dark .select2-container--default .select2-selection--single .select2-selection__rendered {
        color: #f9fafb;
    }
    
    .dark .select2-dropdown {
        background-color: #374151;
        border-color: #4b5563;
    }
    
    .dark .select2-container--default .select2-results__option {
        background-color: #374151;
        color: #f9fafb;
    }
    
    .dark .select2-container--default .select2-results__option--highlighted[aria-selected] {
        background-color: #3b82f6;
    }
    
    /* Плавная прокрутка */
    #messagesContainer {
        scroll-behavior: smooth;
    }
    
    /* Стили для модальных окон */
    .modal-overlay {
        backdrop-filter: blur(4px);
        -webkit-backdrop-filter: blur(4px);
    }
    
    /* Анимация появления модальных окон */
    @keyframes modalFadeIn {
        from {
            opacity: 0;
            transform: scale(0.95);
        }
        to {
            opacity: 1;
            transform: scale(1);
        }
    }
    
    .modal-content {
        animation: modalFadeIn 0.2s ease-out;
    }
    
    /* Стили для sticky системных сообщений */
    .sticky-system-message {
        position: sticky;
        top: 0;
        z-index: 10;
        background: white;
        border-bottom: 1px solid #e5e7eb;
    }
    
    .dark .sticky-system-message {
        background: #111827;
        border-bottom: 1px solid #4b5563;
    }
    
    /* Плавная анимация для системных сообщений */
    .system-message {
        transition: all 0.3s ease;
    }
    
    /* Стили для не-sticky системных сообщений */
    .system-message:not(.sticky) {
        opacity: 0.8;
    }
    
    .system-message:not(.sticky):hover {
        opacity: 1;
    }
    
    /* Дополнительные стили для системных сообщений в темной теме */
    .dark .system-message {
        box-shadow: 0 1px 3px 0 rgba(59, 130, 246, 0.1);
    }
    
    .dark .system-message:hover {
        box-shadow: 0 4px 6px -1px rgba(59, 130, 246, 0.2);
    }

    /* Дополнительные утилитарные классы */
    .line-clamp-2 {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .line-clamp-3 {
        display: -webkit-box;
        -webkit-line-clamp: 3;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
</style>
