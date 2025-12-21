function ensurePlaceholder(column) {
    const realItems = column.querySelectorAll('li[data-lead-id]');
    const emptySlot = column.querySelector('.empty-slot');

    if (realItems.length === 0 && !emptySlot) {
        column.insertAdjacentHTML('beforeend',
            '<li class="empty-slot text-center py-4 text-muted" style="opacity:.6;border:2px dashed #ccc;cursor:default;">Drop lead here</li>'
        );
    }

    if (realItems.length > 0 && emptySlot) {
        emptySlot.remove();
    }
}

function updateCardTotals(card) {
    const leadEls = card.querySelectorAll('.task-column li[data-lead-id]');
    let total = 0;

    leadEls.forEach(li => {
        const val = Number(li.dataset.value);
        if (!isNaN(val)) total += val;
    });

    const totalEl = card.querySelector('.total-value');
    if (totalEl) totalEl.textContent = `Value: $${total.toLocaleString()}`;

    card.dataset.total = total;

    const countEl = card.querySelector('.lead-count');
    if (countEl) countEl.textContent = `Total : ${leadEls.length}`;
}

document.addEventListener("DOMContentLoaded", function () {
    document.querySelectorAll(".task-column").forEach(column => {
        new Sortable(column, {
            group: { name: "leadGroup", pull: true, put: true },
            animation: 150,
            ghostClass: "sortable-ghost",
            fallbackOnBody: true,
            forceFallback: true,

            onEnd: function (evt) {
                const oldColumn = evt.from;
                const newColumn = evt.to;
                ensurePlaceholder(oldColumn);
                ensurePlaceholder(newColumn);

                const leadEl = evt.item;
                const oldCard = evt.from.closest(".status-card");
                const newCard = evt.to.closest(".status-card");
                
                const leadId = leadEl.dataset.leadId;
                const statusId = newCard.dataset.statusId;

                // Use a placeholder in Blade
                // let urlTemplate = "{{ route('back-office.leads.update-status', ':lead') }}";

                // Replace placeholder with actual lead ID
                // let url = urlTemplate.replace(':lead', leadId);

                let url = window.leadConfig.updateStatusUrl.replace(':lead', leadId);
                
                // Send AJAX to update status first
                fetch(url, {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': window.leadConfig.csrfToken
                    },
                    body: JSON.stringify({ status_id: statusId })
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        if (oldCard) updateCardTotals(oldCard);
                        if (newCard) updateCardTotals(newCard);

                        // Show Vuexy Toastr success
                        toastr.success('You moved lead successfully!', 'Success', {
                            closeButton: true,
                            progressBar: true,
                            positionClass: 'toast-top-right',
                            timeOut: 2500
                        });
                    } else {
                        // Optional: revert drag if AJAX fails
                        evt.from.insertBefore(leadEl, evt.from.children[evt.oldIndex]);
                        if (oldCard) updateCardTotals(oldCard);
                        if (newCard) updateCardTotals(newCard);

                        toastr.error('Failed to update lead status', 'Error', {timeOut: 2500});
                    }
                })
                .catch(err => {
                    console.error(err);
                    // Move lead back if needed
                    evt.from.insertBefore(leadEl, evt.from.children[evt.oldIndex]);
                    if (oldCard) updateCardTotals(oldCard);
                    if (newCard) updateCardTotals(newCard);

                    toastr.error('An error occurred', 'Error', {timeOut: 2500});
                });
            }
        });
    });

    // Toggle More Info Section
    document.querySelectorAll('.toggle-more-info').forEach(button => {
        button.addEventListener('click', function() {
            const moreInfo = this.closest('li').querySelector('.more-info');
            if (moreInfo.style.display === 'none' || moreInfo.style.display === '') {
                moreInfo.style.display = 'block';
                this.innerHTML = '<i class="bi bi-chevron-up"></i> Less';
            } else {
                moreInfo.style.display = 'none';
                this.innerHTML = '<i class="bi bi-chevron-down"></i> More';
            }
        });
    });
});