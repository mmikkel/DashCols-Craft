(function (window) {

    if (!window.$) {
        return;
    }

    var DashCols_CpSection = {};

    DashCols_CpSection.init = function () {

        // Init submit button
        var $submitBtn = $('#dashCols-actions .submit:first');
        if ($submitBtn.length > 0) $submitBtn.on('click', $.proxy(onSubmitButtonClick, this));

        // Init tab nav select
        var $tabNav = $('#dashCols-editNavSelect');
        if ($tabNav.length > 0) $tabNav.on('change', $.proxy(onTabNavChange, this));

    }

    function onSubmitButtonClick (e) {

        // Where are we?
        var path = Craft.path.replace('dashcols/layouts/', ''),
            segments = path.split('/');

        if (segments[0] === 'dashcols') segments.shift();

        if (segments.length === 0) return false;

        // Set cached element index to the current section or category group
        switch (segments[0]) {

            case 'entries' :
                
                var selectedSource = '*',
                    sectionId = $('input[name="sectionId"]').val();
                
                if (sectionId !== undefined)
                {
                    selectedSource = 'section:' + sectionId;
                }
                else
                {
                    selectedSource = segments.length > 1 && segments[1] === 'singles' ? 'singles' : '*';
                }
                
                Craft.setLocalStorage('elementindex.Entry', {
                    selectedSource : selectedSource
                });

                console.log('selected source', selectedSource);

                break;
            
            case 'categories' :
                
                var groupId = $('input[name="categoryGroupId"]').val();

                if (groupId !== undefined)
                {
                    Craft.setLocalStorage('elementindex.Category', {
                        selectedSource : 'group:' + groupId
                    });
                }
                else
                {
                    Craft.setLocalStorage('elementindex.Category', '');    
                }

                console.log('selected source', groupId);

                break;

            case 'users' :
                
                var groupId = $('input[name="userGroupId"]').val();
                
                if (groupId !== undefined)
                {
                    Craft.setLocalStorage('elementindex.User', {
                        selectedSource : 'group:' + groupId
                    });
                }
                else
                {
                    Craft.setLocalStorage('elementindex.User', '');    
                }

                console.log('selected source', groupId);
                
                break;

            case 'assets' :
                
                var folderId = $('input[name="assetSourceId"]').val();

                if (folderId !== undefined)
                {
                    Craft.setLocalStorage('elementindex.Asset', {
                        selectedSource : 'folder:' + folderId
                    });
                }
                else
                {
                    Craft.setLocalStorage('elementindex.Asset', '');    
                }

                console.log('selected source', folderId);
                
                break;

        }

    }

    function onTabNavChange (e)
    {
        e.preventDefault();
        window.location.href = $(e.target).val();
    }

    $(document).ready($.proxy(DashCols_CpSection.init, DashCols_CpSection));

} (window));