<x-app-layout>
    <div class="max-w-5xl mx-auto px-6 py-10">

        <h1 class="text-2xl font-bold mb-6">Search Analytics (CTR)</h1>

        {{-- 🔍 Date / Range Filter --}}
        <form method="GET" class="mb-6 flex flex-wrap items-end gap-4">

            {{-- Quick range --}}
            <div>
                <label class="block text-xs text-gray-400 mb-1">Range</label>
                <select name="days"
                        class="rounded-lg bg-white/5 border border-white/10 px-4 py-2 text-sm">
                    <option value="7"  @selected(($days ?? 30) == 7)>Last 7 days</option>
                    <option value="30" @selected(($days ?? 30) == 30)>Last 30 days</option>
                    <option value="90" @selected(($days ?? 30) == 90)>Last 90 days</option>
                </select>
            </div>

            {{-- Custom start date --}}
            <div>
                <label class="block text-xs text-gray-400 mb-1">From</label>
                <input type="date"
                       name="start_date"
                       value="{{ $startDate ?? '' }}"
                       class="rounded-lg bg-white/5 border border-white/10 px-3 py-2 text-sm">
            </div>

            {{-- Custom end date --}}
            <div>
                <label class="block text-xs text-gray-400 mb-1">To</label>
                <input type="date"
                       name="end_date"
                       value="{{ $endDate ?? '' }}"
                       class="rounded-lg bg-white/5 border border-white/10 px-3 py-2 text-sm">
            </div>

            {{-- Apply --}}
            <button type="submit"
                    class="rounded-lg bg-emerald-600 px-5 py-2 text-sm font-semibold hover:bg-emerald-500">
                Apply
            </button>

            {{-- Reset --}}
            <a href="{{ route('admin.analytics') }}"
               class="text-sm text-gray-400 underline hover:text-white">
                Reset
            </a>
        </form>

        {{-- 📊 Analytics Table --}}
        @if(empty($rows))
            <p class="text-gray-500">No search data yet.</p>
        @else
            <div class="overflow-x-auto rounded-lg border border-white/10 bg-white/5">
                <table class="min-w-full text-sm">
                    <thead class="bg-white/10">
                        <tr>
                            <th class="px-4 py-2 text-left">Query</th>
                            <th class="px-4 py-2 text-right">Searches</th>
                            <th class="px-4 py-2 text-right">Clicks</th>
                            <th class="px-4 py-2 text-right">CTR (%)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($rows as $row)
                            <tr class="border-t border-white/10">
                                <td class="px-4 py-2">{{ $row['query'] }}</td>
                                <td class="px-4 py-2 text-right">{{ $row['searches'] }}</td>
                                <td class="px-4 py-2 text-right">{{ $row['clicks'] }}</td>
                                <td class="px-4 py-2 text-right">
                                    {{ number_format($row['ctr'], 2) }}%
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif

    </div>
</x-app-layout>
