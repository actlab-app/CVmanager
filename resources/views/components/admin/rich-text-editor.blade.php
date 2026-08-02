@props([
    'label' => '',
    'lang' => 'tr',
])

@php
    $isTurkish = $lang === 'tr';
    $model = $attributes->wire('model')->value();
@endphp

<div
    class="space-y-2"
    x-data="{
        content: @entangle($attributes->wire('model')),
        mode: 'visual',
        isFocused: false,
        init() {
            this.$nextTick(() => {
                if (this.$refs.editor) {
                    this.$refs.editor.innerHTML = this.content || '';
                }
                if (window.lucide) {
                    window.lucide.createIcons();
                }
            });
            this.$watch('content', (val) => {
                if (this.mode === 'visual' && this.$refs.editor && this.$refs.editor.innerHTML !== (val || '')) {
                    this.$refs.editor.innerHTML = val || '';
                }
            });
        },
        updateContent() {
            if (this.mode === 'visual') {
                const html = this.$refs.editor.innerHTML;
                this.content = (html === '<br>' || html === '<p><br></p>' || html.trim() === '') ? '' : html;
            }
        },
        exec(command, value = null) {
            if (this.mode !== 'visual') return;
            document.execCommand(command, false, value);
            this.updateContent();
            this.$refs.editor.focus();
        },
        formatBlock(tag) {
            if (this.mode !== 'visual') return;
            document.execCommand('formatBlock', false, tag);
            this.updateContent();
            this.$refs.editor.focus();
        },
        insertLink() {
            if (this.mode !== 'visual') return;
            const url = prompt('{{ $isTurkish ? "URL adresini girin (ör: https://...):" : "Enter URL (e.g. https://...):" }}', 'https://');
            if (url) {
                this.exec('createLink', url);
            }
        },
        toggleMode() {
            if (this.mode === 'visual') {
                this.updateContent();
                this.mode = 'code';
            } else {
                this.mode = 'visual';
                this.$nextTick(() => {
                    if (this.$refs.editor) {
                        this.$refs.editor.innerHTML = this.content || '';
                    }
                });
            }
        }
    }"
>
    @if ($label)
        <div class="flex items-center justify-between">
            <label class="block text-sm font-medium text-zinc-800 dark:text-zinc-200">
                {{ $label }}
            </label>
            <button
                type="button"
                x-on:click="toggleMode()"
                class="inline-flex items-center gap-1.5 rounded-lg border border-zinc-200 bg-white px-2.5 py-1 text-xs font-semibold text-zinc-600 shadow-2xs transition hover:border-accent hover:text-accent dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-300 dark:hover:text-white"
            >
                <i data-lucide="code" class="size-3.5"></i>
                <span x-text="mode === 'visual' ? '{{ $isTurkish ? "HTML Kodu" : "HTML Source" }}' : '{{ $isTurkish ? "Görsel Editör" : "Visual Editor" }}'"></span>
            </button>
        </div>
    @endif

    <div class="overflow-hidden rounded-xl border border-zinc-200 bg-white shadow-xs dark:border-zinc-700 dark:bg-zinc-900">
        <!-- Toolbar (Visual Mode Only) -->
        <div
            x-show="mode === 'visual'"
            class="flex flex-wrap items-center gap-1 border-b border-zinc-200 bg-zinc-50/80 px-2 py-1.5 dark:border-zinc-700 dark:bg-zinc-800/80"
        >
            <div class="flex items-center gap-0.5 border-r border-zinc-200 pr-1.5 dark:border-zinc-700">
                <button
                    type="button"
                    x-on:click="exec('bold')"
                    class="rounded-lg p-1.5 text-zinc-600 transition hover:bg-zinc-200/70 hover:text-zinc-900 dark:text-zinc-400 dark:hover:bg-zinc-700 dark:hover:text-zinc-100"
                    title="{{ $isTurkish ? 'Kalın' : 'Bold' }}"
                >
                    <i data-lucide="bold" class="size-4"></i>
                </button>
                <button
                    type="button"
                    x-on:click="exec('italic')"
                    class="rounded-lg p-1.5 text-zinc-600 transition hover:bg-zinc-200/70 hover:text-zinc-900 dark:text-zinc-400 dark:hover:bg-zinc-700 dark:hover:text-zinc-100"
                    title="{{ $isTurkish ? 'İtalik' : 'Italic' }}"
                >
                    <i data-lucide="italic" class="size-4"></i>
                </button>
                <button
                    type="button"
                    x-on:click="exec('underline')"
                    class="rounded-lg p-1.5 text-zinc-600 transition hover:bg-zinc-200/70 hover:text-zinc-900 dark:text-zinc-400 dark:hover:bg-zinc-700 dark:hover:text-zinc-100"
                    title="{{ $isTurkish ? 'Altı Çizili' : 'Underline' }}"
                >
                    <i data-lucide="underline" class="size-4"></i>
                </button>
            </div>

            <div class="flex items-center gap-0.5 border-r border-zinc-200 px-1.5 dark:border-zinc-700">
                <button
                    type="button"
                    x-on:click="formatBlock('<p>')"
                    class="rounded-lg px-2 py-1 text-xs font-bold text-zinc-600 transition hover:bg-zinc-200/70 hover:text-zinc-900 dark:text-zinc-400 dark:hover:bg-zinc-700 dark:hover:text-zinc-100"
                    title="{{ $isTurkish ? 'Normal Metin' : 'Normal Text' }}"
                >
                    P
                </button>
                <button
                    type="button"
                    x-on:click="formatBlock('<h2>')"
                    class="rounded-lg px-2 py-1 text-xs font-bold text-zinc-600 transition hover:bg-zinc-200/70 hover:text-zinc-900 dark:text-zinc-400 dark:hover:bg-zinc-700 dark:hover:text-zinc-100"
                    title="{{ $isTurkish ? 'Başlık 2' : 'Heading 2' }}"
                >
                    H2
                </button>
                <button
                    type="button"
                    x-on:click="formatBlock('<h3>')"
                    class="rounded-lg px-2 py-1 text-xs font-bold text-zinc-600 transition hover:bg-zinc-200/70 hover:text-zinc-900 dark:text-zinc-400 dark:hover:bg-zinc-700 dark:hover:text-zinc-100"
                    title="{{ $isTurkish ? 'Başlık 3' : 'Heading 3' }}"
                >
                    H3
                </button>
            </div>

            <div class="flex items-center gap-0.5 border-r border-zinc-200 px-1.5 dark:border-zinc-700">
                <button
                    type="button"
                    x-on:click="exec('insertUnorderedList')"
                    class="rounded-lg p-1.5 text-zinc-600 transition hover:bg-zinc-200/70 hover:text-zinc-900 dark:text-zinc-400 dark:hover:bg-zinc-700 dark:hover:text-zinc-100"
                    title="{{ $isTurkish ? 'Madde İşaretli Liste' : 'Bullet List' }}"
                >
                    <i data-lucide="list" class="size-4"></i>
                </button>
                <button
                    type="button"
                    x-on:click="exec('insertOrderedList')"
                    class="rounded-lg p-1.5 text-zinc-600 transition hover:bg-zinc-200/70 hover:text-zinc-900 dark:text-zinc-400 dark:hover:bg-zinc-700 dark:hover:text-zinc-100"
                    title="{{ $isTurkish ? 'Numaralı Liste' : 'Numbered List' }}"
                >
                    <i data-lucide="list-ordered" class="size-4"></i>
                </button>
                <button
                    type="button"
                    x-on:click="formatBlock('<blockquote>')"
                    class="rounded-lg p-1.5 text-zinc-600 transition hover:bg-zinc-200/70 hover:text-zinc-900 dark:text-zinc-400 dark:hover:bg-zinc-700 dark:hover:text-zinc-100"
                    title="{{ $isTurkish ? 'Alıntı' : 'Quote' }}"
                >
                    <i data-lucide="quote" class="size-4"></i>
                </button>
            </div>

            <div class="flex items-center gap-0.5 pl-1.5">
                <button
                    type="button"
                    x-on:click="insertLink()"
                    class="rounded-lg p-1.5 text-zinc-600 transition hover:bg-zinc-200/70 hover:text-zinc-900 dark:text-zinc-400 dark:hover:bg-zinc-700 dark:hover:text-zinc-100"
                    title="{{ $isTurkish ? 'Bağlantı Ekle' : 'Add Link' }}"
                >
                    <i data-lucide="link" class="size-4"></i>
                </button>
                <button
                    type="button"
                    x-on:click="exec('removeFormat')"
                    class="rounded-lg p-1.5 text-zinc-600 transition hover:bg-zinc-200/70 hover:text-zinc-900 dark:text-zinc-400 dark:hover:bg-zinc-700 dark:hover:text-zinc-100"
                    title="{{ $isTurkish ? 'Biçimlendirmeyi Temizle' : 'Clear Formatting' }}"
                >
                    <i data-lucide="remove-formatting" class="size-4"></i>
                </button>
            </div>
        </div>

        <!-- Visual WYSIWYG Editable Area -->
        <div
            x-show="mode === 'visual'"
            x-ref="editor"
            contenteditable="true"
            x-on:input="updateContent()"
            x-on:blur="updateContent(); isFocused = false"
            x-on:focus="isFocused = true"
            class="prose prose-sm dark:prose-invert max-w-none min-h-[160px] p-4 text-sm leading-relaxed text-zinc-800 focus:outline-none dark:text-zinc-200 [&_a]:text-accent [&_a]:underline [&_blockquote]:border-l-4 [&_blockquote]:border-accent [&_blockquote]:pl-4 [&_blockquote]:italic [&_h2]:text-base [&_h2]:font-bold [&_h3]:text-sm [&_h3]:font-bold [&_li]:mb-1 [&_ol]:my-2 [&_ol]:list-decimal [&_ol]:pl-5 [&_p]:mb-2 [&_ul]:my-2 [&_ul]:list-disc [&_ul]:pl-5"
            role="textbox"
            aria-multiline="true"
        ></div>

        <!-- Raw HTML Source Code Area -->
        <div x-show="mode === 'code'">
            <textarea
                x-model="content"
                rows="7"
                class="w-full min-h-[160px] border-0 bg-zinc-950 p-4 font-mono text-xs leading-relaxed text-emerald-400 focus:outline-none focus:ring-0 dark:bg-zinc-950"
                placeholder="<p>HTML kodu girin...</p>"
            ></textarea>
        </div>
    </div>
</div>
