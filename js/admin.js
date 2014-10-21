jQuery('document').ready(function() {

        jQuery('#submit-gs-sim').on('click', function(e) {
                wpNavMenu.registerChange();
                gsSimAddWidgettoMenu();
        });


        function gsSimAddWidgettoMenu( ) {
                if(jQuery('#gs-sim-title').val()==='Title'){
                    jQuery('#gs-sim-title').val('');    
                }
                description = jQuery('#gs-sim-html').val();
                menuItems = {};

                processMethod = wpNavMenu.addMenuItemToBottom;

                if ('' === description || 'Text/html/shortcode here!' === description)
                        return false;
                
                var t = jQuery('.gs-sim-div');

                // Show the ajax spinner
                t.find('.spinner').show();
                
                re = /menu-item\[([^\]]*)/;

                m = t.find('.menu-item-db-id');
                listItemDBIDMatch = re.exec(m.attr('name')),
                                listItemDBID = 'undefined' == typeof listItemDBIDMatch[1] ? 0 : parseInt(listItemDBIDMatch[1], 10);
                
                menuItems[listItemDBID] = t.getItemData('add-menu-item', listItemDBID);
                menuItems[listItemDBID]['menu-item-description']= description;
                
                wpNavMenu.addItemToMenu(menuItems, processMethod, function() {
                        // Deselect the items and hide the ajax spinner
                        t.find('.spinner').hide();
                        // Set form back to defaults
                        jQuery('#gs-sim-title').val('').blur();
                        jQuery('#gs-sim-html').val('');
                        
                });
        }
});
