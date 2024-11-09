$(document).ready(function () {
    let deleteTimer;
    let timerCount = 20;
    let nodeIdToDelete = null;

    $('#create-root').on('click', function () {
        $.post('../src/api/add_node.php', { parentId: null, name: 'Root' }, function () {
            loadTree();
        });
    });

    function loadTree() {
        $.get('../src/api/get_tree.php', function (data) {
            $('#tree-container').html(renderTree(data));
        }, 'json');
    }

    function renderTree(nodes) {
        let html = '<ul class="tree">';
        nodes.forEach(node => {
            html += renderNode(node);
        });
        html += '</ul>';
        return html;
    }

    function renderNode(node) {
        const hasChildren = node.children && node.children.length > 0;
        const toggleButton = hasChildren ? '<span class="toggle-button">▶</span>' : '';
        let html = `<li data-id="${node.id}" class="${hasChildren ? 'parent-node' : 'leaf-node'}">
            ${toggleButton} <span class="node-name" ondblclick="editNode(${node.id})">${node.name}</span>
            <span class="actions">
                <button onclick="addNode(${node.id})">+</button>
                <button onclick="prepareDeleteNode(${node.id})">-</button>
                <button onclick="editNode(${node.id})">Rename</button>
            </span>`;

        if (hasChildren) {
            html += '<ul class="hidden">';
            node.children.forEach(child => {
                html += renderNode(child);
            });
            html += '</ul>';
        }
        html += '</li>';
        return html;
    }

    // Toggle visibility of child nodes on triangle click
    $(document).on('click', '.toggle-button', function () {
        const $nextUl = $(this).closest('li').children('ul');
        $nextUl.toggleClass('hidden');
        $(this).closest('li').toggleClass('expanded');
    });

    window.addNode = function (parentId) {
        const name = prompt("Enter node name:");
        if (name) {
            $.post('../src/api/add_node.php', { parentId, name }, function () {
                loadTree();
            });
        }
    };

    window.editNode = function (id) {
        const $nodeName = $(`li[data-id="${id}"] .node-name`);
        const currentName = $nodeName.text();
        $nodeName.replaceWith(`<input type="text" class="editable" data-id="${id}" value="${currentName}" />`);

        const $input = $(`input.editable[data-id="${id}"]`);
        $input.focus();

        $input.on('blur', function () {
            const newName = $(this).val().trim();
            if (newName && newName !== currentName) {
                $.post('../src/api/rename_node.php', { id, newName }, function () {
                    loadTree();
                });
            } else {
                loadTree(); // Reset if no change
            }
        });

        $input.on('keypress', function (e) {
            if (e.which === 13) { // Enter key
                $(this).blur();
            }
        });
    };

    window.prepareDeleteNode = function (id) {
        nodeIdToDelete = id; // Set the node ID to delete
        timerCount = 20; // Reset the timer
        $('#timer').text(timerCount); // Display the timer
        $('#deleteConfirmationModal').modal('show');

        deleteTimer = setInterval(() => {
            timerCount--;
            $('#timer').text(timerCount);
            if (timerCount === 0) {
                clearInterval(deleteTimer);
                $('#deleteConfirmationModal').modal('hide');
            }
        }, 1000);
    };

    $('#confirm-delete').on('click', function () {
        clearInterval(deleteTimer);
        $('#deleteConfirmationModal').modal('hide');
        if (nodeIdToDelete !== null) {
            $.post('../src/api/delete_node.php', { id: nodeIdToDelete }, function () {
                loadTree();
            });
            nodeIdToDelete = null;
        }
    });

    $('#cancel-delete').on('click', function () {
        clearInterval(deleteTimer);
        $('#deleteConfirmationModal').modal('hide');
        nodeIdToDelete = null;
    });

    loadTree();
});
