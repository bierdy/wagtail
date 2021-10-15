class App
{
    modules = [];
    
    elements = {
        'body': '#body',
        'header': '.header',
        'footer': '.footer',
        'modal_alert_link': '.modal-alert-link',
        'modal_confirm_link': '.modal-confirm-link',
        'ck_editor': '.ck-editor',
        'resources_tree': '.resources-tree',
    };
    
    config = {
        'cookie': {},
    };
    
    constructor(modules)
    {
        this.modules = modules;
    }
    
    init()
    {
        this.modules.forEach(function(module) {
            if (module in app)
                app[module]();
            else
                console.log('There is no method ' + module + ' in object app');
        });
    }
    
    nativeModalEvents()
    {
        $(app.elements.modal_alert_link).on('click', function() {
            alert($(this).data('alertLinkText'));
        
            return false;
        });
        
        $(app.elements.modal_confirm_link).on('click', function() {
            let response = confirm($(this).data('confirmLinkText')),
                dropdown_menu = $(this).closest('.dropdown-menu');
    
            if (dropdown_menu.length)
                dropdown_menu.click();
            
            return response;
        });
    }
    
    ckEditor()
    {
        let editors = document.querySelectorAll(app.elements.ck_editor);
        for(let i = 0; i < editors.length; ++i)
        {
            ClassicEditor
                .create(editors[i], {
                    toolbar:
                        [
                            "heading",
                            '|',
                            "bold",
                            "italic",
                            "link",
                            "bulletedList",
                            "numberedList",
                            '|',
                            "indent",
                            "outdent",
                            '|',
                            //"imageUpload",
                            "blockQuote",
                            "insertTable",
                            //"mediaEmbed",
                            "undo",
                            "redo",
                            //'|',
                            //"selectAll",
                            //"ckfinder",
                            //"imageTextAlternative",
                            //"imageStyle:full",
                            //"imageStyle:side",
                            //"tableColumn",
                            //"tableRow",
                            //"mergeTableCells",
                        ],
                    /*
                    ckfinder: {
                        uploadUrl: '/ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Files&responseType=json'
                    },
                    */
                    language: 'en',
                })
                /*
                .then(editor => {
                    console.log(Array.from(editor.ui.componentFactory.names()));
                })
                */
                .catch(error => {
                    console.log(error);
                });
        }
    }
    
    resourcesTree()
    {
        let resources_tree = $(app.elements.resources_tree);
        
        if (! resources_tree.length)
            return;
    
        let branches = resources_tree.find('.branch');
    
        if (! branches.length)
            return;
    
        let cookies_resources_tree = Cookies.get(app.config.cookie.prefix + 'resources_tree');
        
        cookies_resources_tree = cookies_resources_tree ? JSON.parse(cookies_resources_tree) : {'open_branches': []};
        
        let addBranchIdToCookie = function(branch_id)
        {
            branch_id = Number(branch_id);
            
            cookies_resources_tree.open_branches.push(branch_id);
            Cookies.set(
                app.config.cookie.prefix + 'resources_tree',
                JSON.stringify(cookies_resources_tree),
                {
                    domain: app.config.cookie.domain,
                    expires: 365,
                    path: app.config.cookie.path,
                    secure: app.config.cookie.secure,
                    sameSite: app.config.cookie.samesite,
                });
        }
        
        let removeBranchIdFromCookie = function(branch_id)
        {
            branch_id = Number(branch_id);
            
            if (cookies_resources_tree.open_branches.indexOf(branch_id) === -1)
                return;
                
            cookies_resources_tree.open_branches.splice(cookies_resources_tree.open_branches.indexOf(branch_id), 1);
            Cookies.set(
                app.config.cookie.prefix + 'resources_tree',
                JSON.stringify(cookies_resources_tree),
                {
                    domain: app.config.cookie.domain,
                    expires: 365,
                    path: app.config.cookie.path,
                    secure: app.config.cookie.secure,
                    sameSite: app.config.cookie.samesite,
                });
        }
        
        branches.each(function(index, value) {
            let branch = $(value),
                tree = branch.find('.tree:first');
            
            tree.on('show.bs.collapse', function(e)
            {
                if (! $(this).is(e.target))
                    return;
                
                branch.addClass('open');
                addBranchIdToCookie(branch.data('branchId'));
            });
    
            tree.on('hide.bs.collapse', function(e)
            {
                if (! $(this).is(e.target))
                    return;
    
                branch.removeClass('open');
                removeBranchIdFromCookie(branch.data('branchId'));
    
                let child_branches = $(this).find('.branch');
    
                if (child_branches.length)
                    child_branches.each(function(index, value) {
                        removeBranchIdFromCookie($(value).data('branchId'));
                    });
            });
    
            tree.on('hidden.bs.collapse', function(e)
            {
                if (! $(this).is(e.target))
                    return;
    
                let child_branches = $(this).find('.branch'),
                    child_trees = $(this).find('.tree');
    
                child_branches.removeClass('open');
                child_trees.removeClass('show');
            });
        });
        
        $(app.elements.resources_tree + ' .dropdown-item').on('click', function(e) {
            e.stopPropagation();
        });
        
        
        
        
        
        let resources_tree_ = document.querySelector(app.elements.resources_tree);
    
        resources_tree_.addEventListener('dragstart', (e) => {
            if (! e.target)
                return;
            
            e.target.classList.add('selected');
        })
    
        resources_tree_.addEventListener('dragend', (e) => {
            if (! e.target)
                return;
            
            e.target.classList.remove('selected');
            resources_tree_.querySelectorAll('.branch').forEach((element) => {
                element.classList.remove('is-insertable-before');
                element.classList.remove('is-insertable-inside');
                element.classList.remove('is-insertable-after');
            });
        });
    
        resources_tree_.addEventListener('dragover', (e) => {
            e.preventDefault();
    
            let selected_element = resources_tree_.querySelector('.selected');
            let selected_branch = selected_element.closest('.branch');
            let current_element = e.target;
            let current_branch = current_element.closest('.branch');
            let is_movable = selected_element !== current_element && current_element.classList.contains('title');
            
            if (! is_movable)
                return;
    
            let current_element_y_hover_position = getCurrentElementYHoverPosition(e.clientY, current_element);
            
            if (selected_branch.contains(current_element))
                return;
            
            if (current_element_y_hover_position === 'top')
            {
                current_branch.classList.add('is-insertable-before');
                current_branch.classList.remove('is-insertable-inside');
                current_branch.classList.remove('is-insertable-after');
            }
            else if (current_element_y_hover_position === 'center')
            {
                current_branch.classList.remove('is-insertable-before');
                current_branch.classList.add('is-insertable-inside');
                current_branch.classList.remove('is-insertable-after');
            }
            else if (current_element_y_hover_position === 'bottom')
            {
                current_branch.classList.remove('is-insertable-before');
                current_branch.classList.remove('is-insertable-inside');
                current_branch.classList.add('is-insertable-after');
            }
        });
    
        resources_tree_.addEventListener('dragleave', (e) => {
            if (! e.target)
                return;
    
            e.target.closest('.branch').classList.remove('is-insertable-before');
            e.target.closest('.branch').classList.remove('is-insertable-inside');
            e.target.closest('.branch').classList.remove('is-insertable-after');
        });
    
        resources_tree_.addEventListener('drop', (e) => {
            if (! e.target)
                return;
    
            let selected_element = resources_tree_.querySelector('.selected');
            let selected_branch = selected_element.closest('.branch');
            let selected_branch_tree = selected_branch.closest('.tree');
            let current_element = e.target;
            let current_branch = current_element.closest('.branch');
            let current_branch_tree = null;
            
            if (current_branch.classList.contains('is-insertable-inside'))
            {
                current_branch_tree = current_branch.querySelector('.tree');
    
                current_branch_tree.append(selected_branch);
                current_branch.classList.add('childs-exist');
    
                handleSelectedBranchTree(selected_branch_tree);
    
                setBranchParent(selected_branch.dataset.branchId, current_branch.dataset.branchId);
                setBranchOrder(selected_branch.dataset.branchId, current_branch_tree.children.length - 1);
            }
            else
            {
                if (current_branch.classList.contains('is-insertable-before'))
                {
                    current_branch_tree = current_branch.closest('.tree');
                    current_branch_tree.insertBefore(selected_branch, current_branch);
                }
                else if (current_branch.classList.contains('is-insertable-after'))
                {
                    if (current_branch.querySelector('.tree').children.length && current_branch.querySelector('.tree').classList.contains('show'))
                    {
                        current_branch_tree = current_branch.querySelector('.tree');
                        
                        current_branch_tree.insertBefore(selected_branch, current_branch_tree.querySelector('.branch'));
                    }
                    else
                    {
                        current_branch_tree = current_branch.closest('.tree');
                        
                        if (current_branch.nextElementSibling !== null)
                            current_branch_tree.insertBefore(selected_branch, current_branch.nextElementSibling);
                        else
                            current_branch_tree.append(selected_branch);
                    }
                }
    
                if (current_branch_tree === null)
                    return;
    
                handleSelectedBranchTree(selected_branch_tree);
                
                if (selected_branch_tree !== current_branch_tree)
                {
                    if (current_branch_tree.closest('.branch') !== null)
                        setBranchParent(selected_branch.dataset.branchId, current_branch_tree.closest('.branch').dataset.branchId);
                    else
                        setBranchParent(selected_branch.dataset.branchId, 0);
        
                    [].slice.call(current_branch_tree.children).forEach((element, index) => {
                        setBranchOrder(element.dataset.branchId, index);
                    });
                }
            }
    
            current_branch.classList.remove('is-insertable-before');
            current_branch.classList.remove('is-insertable-inside');
            current_branch.classList.remove('is-insertable-after');
        });
    
        const getCurrentElementYHoverPosition = (y_cursor_position, current_element) => {
            let current_element_bounding_client_rect = current_element.getBoundingClientRect();
            let current_element_y_position = current_element_bounding_client_rect.y;
            let current_element_height = current_element_bounding_client_rect.height;
            
            switch(true)
            {
                case y_cursor_position < current_element_y_position + 12 :
                    return 'top';
                case y_cursor_position > current_element_y_position + current_element_height - 12 :
                    return 'bottom';
                default :
                    return 'center';
            }
        };
    
        const setBranchParent = (branch_id, branch_parent_id) => {
            $.ajax({
                url: '/resources/set-parent/' + branch_id + '/' + branch_parent_id,
                method: 'get',
                dataType: 'json',
                success: (data) => {
                
                }
            });
        }
        
        const setBranchOrder = (branch_id, order) => {
            $.ajax({
                url: '/resources/set-order/' + branch_id + '/' + order,
                method: 'get',
                dataType: 'json',
                success: (data) => {
                
                }
            });
        }
    
        const handleSelectedBranchTree = (selected_branch_tree) => {
            if (selected_branch_tree.children.length === 0)
            {
                selected_branch_tree.classList.remove('show');
                selected_branch_tree.closest('.branch').classList.remove('childs-exist', 'open');
                removeBranchIdFromCookie(selected_branch_tree.closest('.branch').dataset.branchId);
            }
            else
            {
                [].slice.call(selected_branch_tree.children).forEach((element, index) => {
                    setBranchOrder(element.dataset.branchId, index);
                });
            }
        }
    }
    
    initConfig() {
        if (window.velldoris_cookie_config)
            app.config.cookie = window.velldoris_cookie_config;
    }
}

let app = new App(['initConfig', 'nativeModalEvents', 'resourcesTree', 'ckEditor']);
app.init();