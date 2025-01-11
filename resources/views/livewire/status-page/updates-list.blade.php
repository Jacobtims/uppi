<div class="mx-auto my-8">
    <div class="relative">
        <!-- Main timeline line -->
        @if($updates->isNotEmpty())
            <div class="absolute left-[16px] top-0 -translate-x-1/2 w-0.5 h-full bg-gray-200"></div>
        @endif

        <div class="space-y-8">
            @foreach($updates as $update)
                <div class="relative">
                    <!-- Timeline dot with status color -->
                    <div @class([
                        'absolute left-[16px] -translate-x-1/2 w-4 h-4 rounded-full border-2 border-white ring-2 z-10 bg-white',
                        'ring-green-500 bg-green-100' => $update->status === \App\Enums\StatusPage\UpdateStatus::COMPLETED,
                        'ring-yellow-500 bg-yellow-100' => in_array($update->status, [
                            \App\Enums\StatusPage\UpdateStatus::MONITORING,
                            \App\Enums\StatusPage\UpdateStatus::RECOVERING,
                            \App\Enums\StatusPage\UpdateStatus::POST_INCIDENT
                        ]),
                        'ring-red-500 bg-red-100' => in_array($update->status, [
                            \App\Enums\StatusPage\UpdateStatus::UNDER_INVESTIGATION,
                            \App\Enums\StatusPage\UpdateStatus::IDENTIFIED,
                            \App\Enums\StatusPage\UpdateStatus::WORK_IN_PROGRESS
                        ]),
                        'ring-blue-500 bg-blue-100' => $update->status === \App\Enums\StatusPage\UpdateStatus::NEW,
                    ])></div>

                    <!-- Content card -->
                    <div class="ml-12 bg-white shadow rounded-lg border border-neutral-100 p-4">
                        <!-- Header -->
                        <div class="flex items-center gap-2">
                            <div @class([
                                'inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-medium',
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
                                    :component="$update->status->getIcon()" 
                                    class="w-3 h-3"
                                />
                                {{ $update->status->getLabel() }}
                            </div>
                            <div @class([
                                'inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-medium',
                                'bg-red-100 text-red-700' => $update->type === \App\Enums\StatusPage\UpdateType::ANOMALY,
                                'bg-yellow-100 text-yellow-700' => $update->type === \App\Enums\StatusPage\UpdateType::MAINTENANCE,
                                'bg-blue-100 text-blue-700' => $update->type === \App\Enums\StatusPage\UpdateType::SCHEDULED_MAINTENANCE,
                                'bg-green-100 text-green-700' => $update->type === \App\Enums\StatusPage\UpdateType::UPDATE,
                            ])>
                                <x-dynamic-component 
                                    :component="$update->type->getIcon()" 
                                    class="w-3 h-3"
                                />
                                {{ $update->type->getLabel() }}
                            </div>
                            @if($update->is_featured)
                                <div class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-medium bg-purple-100 text-purple-700">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-3 h-3">
                                        <path fill-rule="evenodd" d="M10.868 2.884c-.321-.772-1.415-.772-1.736 0l-1.83 4.401-4.753.381c-.833.067-1.171 1.107-.536 1.651l3.62 3.102-1.106 4.637c-.194.813.691 1.456 1.405 1.02L10 15.591l4.069 2.485c.713.436 1.598-.207 1.404-1.02l-1.106-4.637 3.62-3.102c.635-.544.297-1.584-.536-1.65l-4.752-.382-1.831-4.401z" clip-rule="evenodd" />
                                    </svg>
                                    <span>Featured</span>
                                </div>
                            @endif
                            <span class="text-sm text-gray-500 ml-auto">
                                {{ $update->created_at->diffForHumans() }}
                            </span>
                        </div>

                        <!-- Title and content -->
                        <h3 class="text-lg font-semibold mt-2">{{ $update->title }}</h3>
                        <div class="prose prose-sm mt-2 text-gray-600 markdown">
                            {!! str($update->content)->markdown() !!}
                        </div>

                        <!-- Footer -->
                        <div class="mt-4 flex flex-wrap items-center gap-2">
                            @if($update->from || $update->to)
                                <div class="flex items-center gap-2 text-xs text-gray-500">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-4 h-4">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm.75-13a.75.75 0 00-1.5 0v5c0 .414.336.75.75.75h4a.75.75 0 000-1.5h-3.25V5z" clip-rule="evenodd" />
                                    </svg>
                                    @if($update->from)
                                        <span>{{ $update->from->format('M j, Y g:i A') }}</span>
                                    @endif
                                    @if($update->to)
                                        <span class="text-gray-400">→</span>
                                        <span>{{ $update->to->format('M j, Y g:i A') }}</span>
                                    @endif
                                </div>
                            @endif

                            @if($update->monitors->isNotEmpty())
                                <div class="flex flex-wrap gap-2">
                                    @foreach($update->monitors as $monitor)
                                        <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full bg-gray-100 text-gray-700 text-xs">
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-3 h-3">
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