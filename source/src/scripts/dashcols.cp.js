( function ( window ) {

    if ( ! window.$ ) {
        return;
    }

    var DashCols_CpSection = {};

    DashCols_CpSection.init = function () {

        var $submitBtn = $( '#dashCols-actions .submit:first' );

        if ( $submitBtn.length > 0 ) {
            $submitBtn.on( 'click', $.proxy( onSubmitButtonClick, this ) );
        }

    }

    function onSubmitButtonClick ( e ) {

        // Where are we?
        var path = Craft.path.replace( 'dashcols/layouts/', '' ),
            segments = path.split( '/' );

        // Set cached element index to the current section or category group
        switch ( segments[ 0 ] ) {
            case 'category-group' :
                Craft.setLocalStorage( 'elementindex.Category', '' );
                break;
            case 'users' :
                var groupId = $('input[name="userGroupId"]').val();
                if (groupId !== undefined)
                {
                    Craft.setLocalStorage( 'elementindex.User', {
                        selectedSource : 'group:' + groupId
                    } );
                }
                else
                {
                    Craft.setLocalStorage( 'elementindex.User', '' );    
                }
                break;
            default :
                Craft.setLocalStorage( 'elementindex.Entry', '' );
        }

    }

    $( document ).ready( $.proxy( DashCols_CpSection.init, DashCols_CpSection ) );

} ( window ) );