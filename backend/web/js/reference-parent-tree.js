$(document).on(
    'click',
    '.tree-marker',
    function (e) {

        const marker = $(this);

        const header = marker.closest('.tree-node-header');

        if (Number(header.data('has-children')) !== 1) {
            return;
        }

        e.preventDefault();
        e.stopPropagation();

        const children = header
            .closest('.tree-node')
            .children('.tree-node-children');

        children.toggleClass('d-none');

        marker.text(
            children.hasClass('d-none')
                ? '▸'
                : '▾'
        );

    }
);

$(document).on(
    'click',
    '.tree-label',
    function (e) {

        e.preventDefault();
        e.stopPropagation();

        const label = $(this);

        $(document).trigger(
            'reference.item.selected',
            [
                label.data('target'),
                label.data('id'),
                label.data('name')
            ]
        );

    }
);
