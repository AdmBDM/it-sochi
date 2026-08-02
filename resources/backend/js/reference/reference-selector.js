(function () {

    let codeChanged = false;

    const nameField = $('#referenceitem-name');
    const codeField = $('#referenceitem-code');

    codeField.on('input', function () {
        codeChanged = true;
    });

    function transliterate(text) {

        const map = {
            'а':'a','б':'b','в':'v','г':'g','д':'d',
            'е':'e','ё':'e','ж':'zh','з':'z','и':'i',
            'й':'y','к':'k','л':'l','м':'m','н':'n',
            'о':'o','п':'p','р':'r','с':'s','т':'t',
            'у':'u','ф':'f','х':'h','ц':'c','ч':'ch',
            'ш':'sh','щ':'sch','ъ':'','ы':'y','ь':'',
            'э':'e','ю':'yu','я':'ya'
        };

        return text
            .toLowerCase()
            .split('')
            .map(function (char) {
                return map[char] ?? char;
            })
            .join('')
            .replace(/[^a-z0-9]+/g, '_')
            .replace(/_+/g, '_')
            .replace(/^_+|_+$/g, '');
    }

    nameField.on('input', function () {

        if (codeChanged) {
            return;
        }

        codeField.val(
            transliterate($(this).val())
        );

    });

    const selectorConfig = {
        parent: {
            button: '#select-parent-button',
            target: 'parent_id',
            selectedSelector: '#referenceitem-parent_id',
            displaySelector: '#referenceitem-parent_id-name'
        },

        type: {
            button: '#select-type-button',
            target: 'type_id',
            selectedSelector: '#referenceitem-type_id',
            displaySelector: '#referenceitem-type_id-name',
            rootCode: 'type_object'
        }
    };

    let currentSelector = null;
    function getFormModal() {
        return $('#reference-modal');
    }

    function getSelectorModal() {
        return $('#parent-selector-modal');
    }

    $(document).on(
        'show.bs.modal',
        '#parent-selector-modal',
        function () {

            getFormModal().addClass('modal-stack-under');
            getSelectorModal().addClass('modal-stack-top');

        }
    );

    $(document).on(
        'hidden.bs.modal',
        '#parent-selector-modal',
        function () {

            getFormModal().removeClass('modal-stack-under');
            getSelectorModal().removeClass('modal-stack-top');

        }
    );

    function openReferenceSelector(options) {
        currentSelector = options;
        getSelectorModal().modal('show');

        const params = {
            target: options.target,
            selected_id: $(options.selectedSelector).val(),
            exclude_id: $('#referenceitem-id').val()
        };

        if (options.rootCode !== undefined) {
            params.rootCode = options.rootCode;
        }

        $('#parent-selector-content').load(
            '/admin/reference/selector?' + $.param(params)
        );
    }

    Object.values(selectorConfig).forEach(function (config) {
        $(document).on(
            'click',
            config.button,
            function () {
                openReferenceSelector(config);
            }
        );
    });

    $(document).on('reference.item.selected',
        function (e, target, id, name) {

            $(currentSelector.selectedSelector).val(id);
            $(currentSelector.displaySelector).val(name);

            getSelectorModal().modal('hide');
        }
    );

})();
