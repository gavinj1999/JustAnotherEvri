<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { Badge } from '@/components/ui/badge';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Separator } from '@/components/ui/separator';
import { dashboard } from '@/routes';

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Dashboard', href: dashboard() },
            { title: 'Scrapes', href: '/scrapes' },
        ],
    },
});

interface Scrape {
    id: number;
    date: string;
    round: string | null;
    earnings: string | null;
    parcel_count: number | null;
    execution_time: string | null;
    status: 'completed' | 'failed' | 'timeout';
    execution_id: string;
    created_at: string;
}

interface PaginatedScrapes {
    data: Scrape[];
    current_page: number;
    last_page: number;
    per_page: number;
    total: number;
    prev_page_url: string | null;
    next_page_url: string | null;
    links: { url: string | null; label: string; active: boolean }[];
}

interface Stats {
    total_earnings: number;
    total_parcels: number;
    completed_count: number;
    total_count: number;
    avg_earnings: number;
    avg_parcels: number;
}

const props = defineProps<{
    scrapes: PaginatedScrapes;
    stats: Stats;
}>();

function statusVariant(status: string): 'default' | 'secondary' | 'destructive' | 'outline' {
    if (status === 'completed') return 'default';
    if (status === 'failed') return 'destructive';
    return 'secondary';
}

function formatCurrency(value: number | string | null): string {
    if (value === null || value === undefined) return '—';
    return `£${Number(value).toFixed(2)}`;
}

function formatMinutes(value: string | null): string {
    if (!value) return '—';
    const mins = Number(value);
    return mins < 1 ? `${Math.round(mins * 60)}s` : `${mins.toFixed(1)}m`;
}

const successRate = props.stats.total_count > 0
    ? Math.round((props.stats.completed_count / props.stats.total_count) * 100)
    : 0;
</script>

<template>
    <Head title="Scrape Results" />

    <div class="flex flex-col gap-6 p-4">

        <!-- Stats Row -->
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <Card>
                <CardHeader class="pb-2">
                    <CardTitle class="text-sm font-medium text-muted-foreground">Total Earnings</CardTitle>
                </CardHeader>
                <CardContent>
                    <div class="text-2xl font-bold">{{ formatCurrency(stats.total_earnings) }}</div>
                    <p class="text-xs text-muted-foreground">from {{ stats.completed_count }} completed scrapes</p>
                </CardContent>
            </Card>

            <Card>
                <CardHeader class="pb-2">
                    <CardTitle class="text-sm font-medium text-muted-foreground">Total Parcels</CardTitle>
                </CardHeader>
                <CardContent>
                    <div class="text-2xl font-bold">{{ stats.total_parcels.toLocaleString() }}</div>
                    <p class="text-xs text-muted-foreground">avg {{ stats.avg_parcels }} per day</p>
                </CardContent>
            </Card>

            <Card>
                <CardHeader class="pb-2">
                    <CardTitle class="text-sm font-medium text-muted-foreground">Avg Earnings / Day</CardTitle>
                </CardHeader>
                <CardContent>
                    <div class="text-2xl font-bold">{{ formatCurrency(stats.avg_earnings) }}</div>
                    <p class="text-xs text-muted-foreground">completed days only</p>
                </CardContent>
            </Card>

            <Card>
                <CardHeader class="pb-2">
                    <CardTitle class="text-sm font-medium text-muted-foreground">Success Rate</CardTitle>
                </CardHeader>
                <CardContent>
                    <div class="text-2xl font-bold">{{ successRate }}%</div>
                    <p class="text-xs text-muted-foreground">{{ stats.completed_count }} / {{ stats.total_count }} scrapes</p>
                </CardContent>
            </Card>
        </div>

        <Separator />

        <!-- Scrapes Table -->
        <Card>
            <CardHeader>
                <CardTitle>Recent Scrapes</CardTitle>
            </CardHeader>
            <CardContent class="p-0">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b bg-muted/50 text-left text-xs font-medium uppercase tracking-wide text-muted-foreground">
                                <th class="px-4 py-3">Date</th>
                                <th class="px-4 py-3">Round</th>
                                <th class="px-4 py-3">Status</th>
                                <th class="px-4 py-3 text-right">Earnings</th>
                                <th class="px-4 py-3 text-right">Parcels</th>
                                <th class="px-4 py-3 text-right">Run Time</th>
                                <th class="px-4 py-3 text-right">Recorded</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y">
                            <tr v-if="scrapes.data.length === 0">
                                <td colspan="7" class="px-4 py-8 text-center text-muted-foreground">
                                    No scrape results yet.
                                </td>
                            </tr>
                            <tr
                                v-for="scrape in scrapes.data"
                                :key="scrape.id"
                                class="hover:bg-muted/30 transition-colors"
                            >
                                <td class="px-4 py-3 font-medium">{{ scrape.date }}</td>
                                <td class="px-4 py-3 text-muted-foreground">{{ scrape.round ?? '—' }}</td>
                                <td class="px-4 py-3">
                                    <Badge :variant="statusVariant(scrape.status)">
                                        {{ scrape.status }}
                                    </Badge>
                                </td>
                                <td class="px-4 py-3 text-right tabular-nums">
                                    {{ formatCurrency(scrape.earnings) }}
                                </td>
                                <td class="px-4 py-3 text-right tabular-nums">
                                    {{ scrape.parcel_count ?? '—' }}
                                </td>
                                <td class="px-4 py-3 text-right tabular-nums text-muted-foreground">
                                    {{ formatMinutes(scrape.execution_time) }}
                                </td>
                                <td class="px-4 py-3 text-right text-xs text-muted-foreground">
                                    {{ scrape.created_at }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div v-if="scrapes.last_page > 1" class="flex items-center justify-between border-t px-4 py-3 text-sm text-muted-foreground">
                    <span>{{ scrapes.total }} total</span>
                    <div class="flex gap-1">
                        <Link
                            v-for="link in scrapes.links"
                            :key="link.label"
                            :href="link.url ?? ''"
                            :class="[
                                'px-2 py-1 rounded text-xs',
                                link.active ? 'bg-primary text-primary-foreground' : 'hover:bg-muted',
                                !link.url ? 'pointer-events-none opacity-40' : '',
                            ]"
                            v-html="link.label"
                        />
                    </div>
                </div>
            </CardContent>
        </Card>
    </div>
</template>
