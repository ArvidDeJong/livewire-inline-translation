<span>
    @if ($isAuthorized)
        <span wire:click="openModal"
            style="cursor: pointer; border-bottom: 1px dashed #3b82f6; transition: border-color 0.2s;"
            onmouseover="this.style.borderBottomColor='#1d4ed8'"
            onmouseout="this.style.borderBottomColor='#3b82f6'">{!! $translationValue !!}</span>

        @if ($showModal)
            <template x-teleport="#inline-translation-modals">
                <div style="position: fixed; inset: 0; z-index: 9999; display: flex; align-items: center; justify-content: center;"
                    wire:click.self="closeModal">
                    <div style="position: fixed; inset: 0; background-color: rgba(0, 0, 0, 0.5); backdrop-filter: blur(4px);"
                        wire:click="closeModal"></div>

                    <div
                        style="position: relative; background-color: white; border-radius: 12px; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25); max-width: 32rem; width: 100%; margin: 1rem; padding: 1.5rem; z-index: 10000;">
                        <div
                            style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1rem;">
                            <h3 style="font-size: 1.125rem; font-weight: 600; color: #111827; margin: 0;">
                                Edit Translation
                            </h3>
                            <button wire:click="closeModal" type="button"
                                style="background: none; border: none; cursor: pointer; padding: 0.5rem; color: #6b7280; border-radius: 0.375rem;"
                                onmouseover="this.style.backgroundColor='#f3f4f6'"
                                onmouseout="this.style.backgroundColor='transparent'">
                                <svg style="width: 1.25rem; height: 1.25rem;" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        </div>

                        <div style="margin-bottom: 1rem;">
                            <label
                                style="display: block; font-size: 0.875rem; font-weight: 500; color: #374151; margin-bottom: 0.5rem;">
                                Key
                            </label>
                            <div
                                style="padding: 0.5rem 0.75rem; background-color: #f9fafb; border: 1px solid #e5e7eb; border-radius: 0.375rem; font-family: monospace; font-size: 0.875rem; color: #6b7280;">
                                {{ $translationKey }}
                            </div>
                        </div>

                        <div style="margin-bottom: 1.5rem;">
                            <label
                                style="display: block; font-size: 0.875rem; font-weight: 500; color: #374151; margin-bottom: 0.5rem;">
                                Translation
                            </label>
                            <textarea wire:model="translationValue" rows="4"
                                style="width: 100%; padding: 0.75rem; border: 1px solid #d1d5db; border-radius: 0.375rem; font-size: 0.875rem; resize: vertical; outline: none; box-sizing: border-box; background-color: #ffffff; color: #111827;"
                                onfocus="this.style.borderColor='#3b82f6'; this.style.boxShadow='0 0 0 3px rgba(59, 130, 246, 0.1)';"
                                onblur="this.style.borderColor='#d1d5db'; this.style.boxShadow='none';"></textarea>
                        </div>

                        <div style="display: flex; justify-content: flex-end; gap: 0.75rem;">
                            <button wire:click="closeModal" type="button"
                                style="padding: 0.5rem 1rem; background-color: white; border: 1px solid #d1d5db; border-radius: 0.375rem; font-size: 0.875rem; font-weight: 500; color: #374151; cursor: pointer;"
                                onmouseover="this.style.backgroundColor='#f9fafb'"
                                onmouseout="this.style.backgroundColor='white'">
                                Cancel
                            </button>
                            <button wire:click="save" type="button"
                                style="padding: 0.5rem 1rem; background-color: #3b82f6; border: none; border-radius: 0.375rem; font-size: 0.875rem; font-weight: 500; color: white; cursor: pointer;"
                                onmouseover="this.style.backgroundColor='#2563eb'"
                                onmouseout="this.style.backgroundColor='#3b82f6'">
                                Save
                            </button>
                        </div>
                    </div>
                </div>
            </template>
        @endif
    @else
        {!! $translationValue !!}
    @endif
</span>
