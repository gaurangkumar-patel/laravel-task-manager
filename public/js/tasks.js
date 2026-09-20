document.addEventListener('DOMContentLoaded', () => {
    const list = document.getElementById('task-list');

    if (!list) {
        return;
    }

    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
    const status = document.getElementById('save-status');
    let draggedItem = null;

    list.addEventListener('dragstart', (event) => {
        const item = event.target.closest('.task-item');

        if (!item) {
            return;
        }

        draggedItem = item;
        item.classList.add('dragging');
        event.dataTransfer.effectAllowed = 'move';
    });

    list.addEventListener('dragover', (event) => {
        event.preventDefault();

        if (!draggedItem) {
            return;
        }

        const afterElement = getDragAfterElement(list, event.clientY);

        if (afterElement === null) {
            list.appendChild(draggedItem);
        } else {
            list.insertBefore(draggedItem, afterElement);
        }
    });

    list.addEventListener('dragend', async () => {
        if (!draggedItem) {
            return;
        }

        draggedItem.classList.remove('dragging');
        draggedItem = null;
        updatePriorityLabels(list);

        if (status) {
            status.textContent = 'Saving priority...';
        }

        try {
            const response = await fetch(list.dataset.reorderUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                },
                body: JSON.stringify({
                    project_id: Number(list.dataset.projectId),
                    order: [...list.querySelectorAll('.task-item')].map((item) => Number(item.dataset.taskId)),
                }),
            });

            if (!response.ok) {
                throw new Error('Unable to save the new order.');
            }

            if (status) {
                status.textContent = 'Priority saved.';
            }
        } catch (error) {
            if (status) {
                status.textContent = 'Could not save priority. Reloading...';
            }

            window.setTimeout(() => window.location.reload(), 800);
        }
    });
});

function getDragAfterElement(container, mouseY) {
    const items = [...container.querySelectorAll('.task-item:not(.dragging)')];

    return items.reduce((closest, item) => {
        const box = item.getBoundingClientRect();
        const offset = mouseY - box.top - box.height / 2;

        if (offset < 0 && offset > closest.offset) {
            return { offset, element: item };
        }

        return closest;
    }, { offset: Number.NEGATIVE_INFINITY, element: null }).element;
}

function updatePriorityLabels(container) {
    container.querySelectorAll('.task-item').forEach((item, index) => {
        const badge = item.querySelector('.priority-badge');

        if (badge) {
            badge.textContent = `#${index + 1}`;
        }
    });
}
