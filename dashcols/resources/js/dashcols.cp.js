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

        // Set cached element index to the current section or category group
        switch (segments[0]) {

            case 'entries' :
                Craft.setLocalStorage('elementindex.Entry', '');
                break;
            
            case 'categories' :
                Craft.setLocalStorage('elementindex.Category', '');
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