<style>
    .pro-roadmap-wrapper {
        padding: 2rem;
        font-family: 'Poppins', sans-serif;
    }

    .pro-roadmap-timeline {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
        gap: 3rem 2rem;
        position: relative;
    }

    .milestone {
        position: relative;
        background: #fff;
        border-top: 5px solid var(--accent-color);
        border-radius: 8px;
        padding: 1rem;
        box-shadow: 0 2px 6px rgba(0,0,0,0.08);
        transition: transform 0.3s ease;
    }

    .milestone:hover {
        transform: translateY(-4px);
    }

    .milestone-header {
        font-size: 0.85rem;
        font-weight: 600;
        color: var(--accent-color);
        margin-bottom: 0.5rem;
    }

    .milestone-content h6 {
        font-size: 1rem;
        font-weight: 600;
        margin-bottom: 0.25rem;
    }

    .milestone-content p {
        font-size: 0.875rem;
        color: #495057;
        margin: 0;
    }
    .arrow {
        position: absolute;
        width: 14px;
        height: 14px;
        z-index: 99;
        background: var(--accent-color);
        opacity: 0.9;
    }

    .arrow-right {
        top: 50%;
        right: -22px;
        transform: translateY(-50%);
        clip-path: polygon(0 50%, 100% 0, 100% 100%);
    }

    .arrow-left {
        top: 50%;
        left: -22px;
        transform: translateY(-50%);
        clip-path: polygon(100% 50%, 0 0, 0 100%);
    }

    .arrow-down {
        left: 50%;
        bottom: -18px;
        transform: translateX(-50%);
        clip-path: polygon(50% 100%, 0 0, 100% 0);
    }



</style>

<div class="pro-roadmap-wrapper">
    <div class="pro-roadmap-timeline" id="roadmapTimeline">
        @foreach($customer_log as $item)
            @php
                $color = match($item->type) {
                    'Approve Loan' => '#198754',
                    'Penalty' => '#dc3545',
                    'Create Loan' => '#0d6efd',
                    'Payment' => '#6c757d',
                    'Blacklist' => '#ff0000',
                    'Remove Blacklist' => '#20c997',
                    default => '#6c757d'
                };
            @endphp
            <div class="milestone" style="--accent-color: {{ $color }}">
                <div class="milestone-header">{{ $item->date }} ({{ $item->time }})</div>
                <div class="milestone-content">
                    <h6>{{ $item->type }} <small class="text-muted">({{ $item->Full_Name }})</small></h6>
                    <p>{{ $item->description }}</p>
                </div>
            </div>
        @endforeach
    </div>
</div>

<script>
    function addZigZagArrows() {
        const cards = Array.from(document.querySelectorAll('#roadmapTimeline .milestone'));

        // Clean previous arrows
        cards.forEach(card => {
            card.querySelectorAll('.arrow').forEach(a => a.remove());
        });

        // Step 1: Group cards into rows based on offsetTop
        const rows = [];
        cards.forEach(card => {
            const top = card.offsetTop;
            const row = rows.find(r => Math.abs(r[0].offsetTop - top) < 5);
            row ? row.push(card) : rows.push([card]);
        });

        // Step 2: Apply arrows based on flow direction
        rows.forEach((row, rowIndex) => {
            const isEvenRow = rowIndex % 2 === 0;
            const flow = isEvenRow ? row : [...row].reverse();

            flow.forEach((card, i) => {
                const isFirstInFlow = i === 0;
                const isLastInFlow = i === flow.length - 1;
                const isLastRow = rowIndex === rows.length - 1;

                // Skip first card for horizontal arrow
                if (!isFirstInFlow) {
                    const arrow = document.createElement('div');
                    arrow.classList.add('arrow', isEvenRow ? 'arrow-left' : 'arrow-right');
                    card.appendChild(arrow);
                }

                // Always add down arrow to the true last card of this row (not just flow)
                if (!isLastRow && i === flow.length - 1) {
                    const downArrow = document.createElement('div');
                    downArrow.classList.add('arrow', 'arrow-down');
                    card.appendChild(downArrow);
                }
            });
        });


    }

    // Fire after DOM ready
    document.addEventListener('DOMContentLoaded', () => {
        setTimeout(addZigZagArrows, 300);
    });

    // Fire again after tab content loads (AJAX)
    $(document).ajaxComplete((e, xhr, settings) => {
        if (settings.url.includes('/kyc/RoadMap')) {
            setTimeout(addZigZagArrows, 300);
        }
    });
</script>












