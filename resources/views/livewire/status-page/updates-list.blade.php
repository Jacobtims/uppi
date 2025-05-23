<div class="my-8">
    @if($updates->isNotEmpty())
        <h2 class="font-medium text-neutral-900 text-lg my-2 pl-1">System Updates</h2>
    @endif
    
    <div class="relative">
        <!-- Main timeline line -->
        @if($updates->isNotEmpty())
            <div class="absolute left-4 sm:left-[16px] top-3 -translate-x-1/2 w-[3px] h-[calc(100%-32px)] bg-neutral-200 rounded-full"></div>
        @endif

        <div class="space-y-6">
            @foreach($updates as $update)
                <div class="relative">
                    <!-- Timeline dot with status color -->
                    <div @class([
                        'absolute left-4 sm:left-[16px] top-3 -translate-x-1/2 w-[10px] h-[10px] rounded-full border-[2px] border-white ring-[2px] z-10 shadow-sm',
                        'ring-green-500 bg-green-500' => $update->status === \App\Enums\StatusPage\UpdateStatus::COMPLETED,
                        'ring-yellow-500 bg-yellow-500' => in_array($update->status, [
                            \App\Enums\StatusPage\UpdateStatus::MONITORING,
                            \App\Enums\StatusPage\UpdateStatus::RECOVERING,
                            \App\Enums\StatusPage\UpdateStatus::POST_INCIDENT
                        ]),
                        'ring-red-500 bg-red-500' => in_array($update->status, [
                            \App\Enums\StatusPage\UpdateStatus::UNDER_INVESTIGATION,
                            \App\Enums\StatusPage\UpdateStatus::IDENTIFIED,
                            \App\Enums\StatusPage\UpdateStatus::WORK_IN_PROGRESS
                        ]),
                        'ring-blue-500 bg-blue-500' => $update->status === \App\Enums\StatusPage\UpdateStatus::NEW,
                    ])></div>

                    <!-- Content card -->
                    <div class="ml-10 sm:ml-14 bg-white rounded-2xl shadow-sm border border-neutral-100 p-6">
                        <!-- Header -->
                        <div class="flex flex-wrap items-center gap-2.5">
                            <div @class([
                                'inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium',
                                'bg-green-100 text-green-700' => $update->status === \App\Enums\StatusPage\UpdateStatus::COMPLETED,
                                'bg-yellow-100 text-yellow-700' => in_array($update->status, [
                                    \App\Enums\StatusPage\UpdateStatus::MONITORING,
                                    \App\Enums\StatusPage\UpdateStatus::RECOVERING,
                                    \App\Enums\StatusPage\UpdateStatus::POST_INCIDENT
                                ]),
                                'bg-red-100 text-red-700' => in_array($update->status, [
                                    \App\Enums\StatusPage\UpdateStatus::UNDER_INVESTIGATION,
                                    \App\Enums\StatusPage\UpdateStatus::IDENTIFIED,
                                    \App\Enums\StatusPage\UpdateStatus::WORK_IN_PROGRESS
                                ]),
                                'bg-blue-100 text-blue-700' => $update->status === \App\Enums\StatusPage\UpdateStatus::NEW,
                            ])>
                                <x-dynamic-component 
                                    :component="$update->status?->getIcon()" 
                                    class="w-3.5 h-3.5"
                                />
                                {{ $update->status->getLabel() }}
                            </div>
                            <div @class([
                                'inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium',
                                'bg-red-100 text-red-700' => $update->type === \App\Enums\StatusPage\UpdateType::ANOMALY,
                                'bg-yellow-100 text-yellow-700' => $update->type === \App\Enums\StatusPage\UpdateType::MAINTENANCE,
                                'bg-blue-100 text-blue-700' => $update->type === \App\Enums\StatusPage\UpdateType::SCHEDULED_MAINTENANCE,
                                'bg-green-100 text-green-700' => $update->type === \App\Enums\StatusPage\UpdateType::UPDATE,
                            ])>
                                <x-dynamic-component 
                                    :component="$update->type?->getIcon()" 
                                    class="w-3.5 h-3.5"
                                />
                                {{ $update->type->getLabel() }}
                            </div>
                            @if($update->is_featured)
                                <div class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-purple-100 text-purple-700">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-3.5 h-3.5">
                                        <path fill-rule="evenodd" d="M10.868 2.884c-.321-.772-1.415-.772-1.736 0l-1.83 4.401-4.753.381c-.833.067-1.171 1.107-.536 1.651l3.62 3.102-1.106 4.637c-.194.813.691 1.456 1.405 1.02L10 15.591l4.069 2.485c.713.436 1.598-.207 1.404-1.02l-1.106-4.637 3.62-3.102c.635-.544.297-1.584-.536-1.65l-4.752-.382-1.831-4.401z" clip-rule="evenodd" />
                                    </svg>
                                    <span>Featured</span>
                                </div>
                            @endif
                            <span class="text-sm text-neutral-500 sm:ml-auto mt-2 sm:mt-0 w-full sm:w-auto order-last sm:order-none">
                                {{ $update->created_at->diffForHumans() }}
                            </span>
                        </div>

                        <!-- Title and content -->
                        <h3 class="text-lg font-medium text-neutral-900 mt-4">{{ $update->title }}</h3>
                        <div class="prose prose-sm mt-3 text-neutral-600 markdown max-w-none">
                            {!! str($update->content)->markdown() !!}
                        </div>

                        <!-- Footer -->
                        <div class="mt-5 flex flex-wrap items-center gap-3">
                            @if($update->from || $update->to)
                                <div class="flex items-center gap-2 text-xs text-neutral-500 flex-wrap">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-4 h-4 shrink-0">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm.75-13a.75.75 0 00-1.5 0v5c0 .414.336.75.75.75h4a.75.75 0 000-1.5h-3.25V5z" clip-rule="evenodd" />
                                    </svg>
                                    <div class="flex items-center gap-2 flex-wrap">
                                        @if($update->from)
                                            <span>{{ $update->from->format('M j, Y g:i A') }}</span>
                                        @endif
                                        @if($update->to)
                                            <span class="text-neutral-400 shrink-0">→</span>
                                            <span>{{ $update->to->format('M j, Y g:i A') }}</span>
                                        @endif
                                    </div>
                                </div>
                            @endif

                            @if($update->monitors->isNotEmpty())
                                <div class="flex flex-wrap gap-2">
                                    @foreach($update->monitors as $monitor)
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-neutral-100 text-neutral-700 text-xs">
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-3.5 h-3.5 shrink-0">
                                                <path fill-rule="evenodd" d="M2 10a8 8 0 1116 0 8 8 0 01-16 0zm8 1a1 1 0 100-2 1 1 0 000 2zm-3-1a1 1 0 11-2 0 1 1 0 012 0zm7 1a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd" />
                                            </svg>
                                            {{ $monitor->name }}
                                        </span>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div> 