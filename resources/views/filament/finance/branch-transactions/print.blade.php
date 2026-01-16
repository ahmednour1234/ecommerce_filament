<x-filament::page>
    <style>
        /* ===== Print ===== */
        @media print {
            .no-print { display: none !important; }
            body { background: #fff !important; }
            .tx-shell { padding: 0 !important; }
            .tx-card { box-shadow: none !important; border: 1px solid #e5e7eb !important; }
        }

        /* ===== Layout ===== */
        .tx-shell { max-width: 920px; margin: 0 auto; }
        .tx-card {
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 18px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(15, 23, 42, 0.06);
        }

        .tx-header {
            padding: 18px 20px;
            background: linear-gradient(135deg, #0f172a, #1f2937);
            color: #fff;
        }

        .tx-header-top {
            display: flex;
            justify-content: space-between;
            gap: 12px;
            align-items: start;
        }

        .tx-title {
            font-size: 16px;
            font-weight: 800;
            letter-spacing: .2px;
            margin: 0;
        }

        .tx-sub {
            margin-top: 4px;
            font-size: 12px;
            opacity: .85;
        }

        .tx-badges {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
            justify-content: end;
        }

        .badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 6px 10px;
            border-radius: 999px;
            font-weight: 700;
            font-size: 12px;
            background: rgba(255,255,255,0.14);
            border: 1px solid rgba(255,255,255,0.18);
            white-space: nowrap;
        }

        .dot {
            width: 8px;
            height: 8px;
            border-radius: 999px;
            background: #94a3b8;
            display: inline-block;
        }

        .tx-body { padding: 18px 20px; }

        .grid {
            display: grid;
            grid-template-columns: repeat(12, minmax(0, 1fr));
            gap: 12px;
        }

        .field {
            grid-column: span 6;
            border: 1px solid #eef2f7;
            background: #fafafa;
            border-radius: 14px;
            padding: 12px 12px;
        }

        .field.full { grid-column: span 12; }
        .field.half { grid-column: span 6; }
        .field.third { grid-column: span 4; }

        .label {
            font-size: 12px;
            font-weight: 700;
            color: #475569;
            margin-bottom: 6px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .value {
            font-size: 14px;
            font-weight: 800;
            color: #0f172a;
            line-height: 1.4;
            word-break: break-word;
        }

        .muted { color: #64748b; font-weight: 700; }
        .amount {
            font-size: 18px;
            font-weight: 900;
            letter-spacing: .2px;
        }

        .tx-footer {
            padding: 12px 20px;
            border-top: 1px dashed #e5e7eb;
            background: #fff;
            display: flex;
            justify-content: space-between;
            gap: 12px;
            align-items: center;
            color: #64748b;
            font-size: 12px;
            font-weight: 700;
        }

        .divider {
            height: 1px;
            background: linear-gradient(90deg, transparent, #e5e7eb, transparent);
            margin: 14px 0;
        }

        @media (max-width: 768px) {
            .field { grid-column: span 12; }
            .tx-header-top { flex-direction: column; align-items: start; }
            .tx-badges { justify-content: start; }
        }
    </style>

    @php
        $record = $this->record;

        $docNo = $record->document_no ?? '-';
        $date = optional($record->transaction_date)->format('Y-m-d') ?? '-';

        $branch = $record->branch?->name ?? '-';
        $country = $record->country?->name ?? '-';

        $typeLabel = $record->type === 'income'
            ? tr('forms.branch_tx.type_income', [], null, 'dashboard')
            : tr('forms.branch_tx.type_expense', [], null, 'dashboard');

        $statusLabel = tr('tables.branch_tx.status_' . $record->status, [], null, 'dashboard');

        $amount = number_format((float) $record->amount, 2);
        $currency = $record->currency?->code ?? '';

        $receiver = $record->receiver_name ?? '-';
        $paymentMethod = $record->payment_method ?? '-';
        $referenceNo = $record->reference_no ?? '-';
        $notes = $record->notes ?? '-';

        // badges colors (inline with dots)
        $typeDot = $record->type === 'income' ? '#22c55e' : '#f97316';
        $statusDot = match($record->status) {
            'approved' => '#22c55e',
            'pending'  => '#f59e0b',
            'rejected' => '#ef4444',
            default    => '#94a3b8',
        };
    @endphp

    <div class="tx-shell space-y-4">
        <div class="no-print flex justify-end gap-2">
            <x-filament::button icon="heroicon-o-printer" onclick="window.print()">
                {{ tr('actions.print', [], null, 'dashboard') }}
            </x-filament::button>

            <x-filament::button color="gray" icon="heroicon-o-arrow-left" tag="a"
                href="{{ \App\Filament\Resources\Finance\BranchTransactionResource::getUrl('index') }}">
                {{ tr('actions.back', [], null, 'dashboard') }}
            </x-filament::button>
        </div>

        <div class="tx-card">
            {{-- Header --}}
            <div class="tx-header">
                <div class="tx-header-top">
                    <div>
                        <p class="tx-title">
                            {{ tr('navigation.finance.branch_transactions', [], null, 'dashboard') ?? 'Branch Transaction' }}
                        </p>
                        <div class="tx-sub">
                            <span class="muted">{{ tr('tables.branch_tx.document_no', [], null, 'dashboard') }}:</span>
                            <span style="font-weight: 900; color: #fff;">{{ $docNo }}</span>
                            <span style="opacity:.6;">•</span>
                            <span class="muted">{{ tr('tables.branch_tx.transaction_date', [], null, 'dashboard') }}:</span>
                            <span style="font-weight: 900; color: #fff;">{{ $date }}</span>
                        </div>
                    </div>

                    <div class="tx-badges">
                        <span class="badge" title="Type">
                            <span class="dot" style="background: {{ $typeDot }}"></span>
                            {{ tr('tables.branch_tx.type', [], null, 'dashboard') }}: {{ $typeLabel }}
                        </span>

                        <span class="badge" title="Status">
                            <span class="dot" style="background: {{ $statusDot }}"></span>
                            {{ tr('tables.branch_tx.status', [], null, 'dashboard') }}: {{ $statusLabel }}
                        </span>
                    </div>
                </div>
            </div>

            {{-- Body --}}
            <div class="tx-body">
                <div class="grid">
                    <div class="field half">
                        <div class="label"># {{ tr('tables.branch_tx.branch', [], null, 'dashboard') }}</div>
                        <div class="value">{{ $branch }}</div>
                    </div>

                    <div class="field half">
                        <div class="label">🌍 {{ tr('tables.branch_tx.country', [], null, 'dashboard') }}</div>
                        <div class="value">{{ $country }}</div>
                    </div>

                    <div class="field half">
                        <div class="label">💳 {{ tr('forms.branch_tx.payment_method', [], null, 'dashboard') }}</div>
                        <div class="value">{{ $paymentMethod }}</div>
                    </div>

                    <div class="field half">
                        <div class="label">🔖 {{ tr('forms.branch_tx.reference_no', [], null, 'dashboard') }}</div>
                        <div class="value">{{ $referenceNo }}</div>
                    </div>

                    <div class="field half">
                        <div class="label">👤 {{ tr('tables.branch_tx.receiver_name', [], null, 'dashboard') }}</div>
                        <div class="value">{{ $receiver }}</div>
                    </div>

                    <div class="field half">
                        <div class="label">💰 {{ tr('tables.branch_tx.amount', [], null, 'dashboard') }}</div>
                        <div class="value amount">
                            {{ $amount }} <span style="font-size: 12px; font-weight: 900; opacity: .7;">{{ $currency }}</span>
                        </div>
                    </div>

                    <div class="field full">
                        <div class="label">📝 {{ tr('forms.branch_tx.notes', [], null, 'dashboard') }}</div>
                        <div class="value">{{ $notes }}</div>
                    </div>
                </div>

                <div class="divider"></div>

                <div class="grid">
                    <div class="field third">
                        <div class="label">🕒 Created</div>
                        <div class="value">{{ optional($record->created_at)->format('Y-m-d H:i') ?? '-' }}</div>
                    </div>

                    <div class="field third">
                        <div class="label">🧾 Updated</div>
                        <div class="value">{{ optional($record->updated_at)->format('Y-m-d H:i') ?? '-' }}</div>
                    </div>

                    <div class="field third">
                        <div class="label">🆔 ID</div>
                        <div class="value">{{ $record->id ?? '-' }}</div>
                    </div>
                </div>
            </div>

            {{-- Footer --}}
            <div class="tx-footer">
                <div>
                    {{ tr('general.printed_on', [], null, 'dashboard') ?? 'Printed on' }}:
                    <span style="color:#0f172a; font-weight: 900;">{{ now()->format('Y-m-d H:i') }}</span>
                </div>
                <div style="display:flex; gap:10px; align-items:center;">
                    <span style="opacity:.6;">•</span>
                    <span>{{ e(config('app.name')) }}</span>
                </div>
            </div>
        </div>
    </div>
</x-filament::page>
