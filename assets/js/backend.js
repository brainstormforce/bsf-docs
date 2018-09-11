
    function to_change_taxonomy(element)
        {
            //select the default category (0)
            jQuery('#to_form #cat').val(jQuery("#to_form #cat option:first").val());
            jQuery('#to_form').submit();
        }
        
    var convArrToObj = function(array){
                            var thisEleObj = new Object();
                            if(typeof array == "object"){
                                for(var i in array){
                                    var thisEle = convArrToObj(array[i]);
                                    thisEleObj[i] = thisEle;
                                }
                            }else {
                                thisEleObj = array;
                            }
                            return thisEleObj;
                        }

                         jQuery(document).ready(function() {
                        
                        jQuery("ul.sortable").sortable({
                                'tolerance':'intersect',
                                'cursor':'pointer',
                                'items':'> li',
                                'axi': 'y',
                                'placeholder':'placeholder',
                                'nested': 'ul'
                            });
                          
                        jQuery(".save-order").bind( "click", function() {
                                var mySortable = new Array();
                                jQuery(".sortable").each(  function(){
                                    
                                    var serialized = jQuery(this).sortable("serialize");
                                    
                                    var parent_tag = jQuery(this).parent().get(0).tagName;
                                    parent_tag = parent_tag.toLowerCase()
                                    if (parent_tag == 'li')
                                        {
                                            // 
                                            var tag_id = jQuery(this).parent().attr('id');
                                            mySortable[tag_id] = serialized;
                                        }
                                        else
                                        {
                                            //
                                            mySortable[0] = serialized;
                                        }
                                });
                                
                                //serialize the array
                                var serialize_data = JSON.stringify( convArrToObj(mySortable));
                                console.log( BSFDocs.ajaxurl );                                               
                                jQuery.post( BSFDocs.ajaxurl, { action:'update-taxonomy-order', order: serialize_data }, function() {
                                    jQuery("#ajax-response").html('<div class="message updated fade"><p>Category Items Order Updated</p></div>');
                                    jQuery("#ajax-response div").delay(3000).hide("slow");
                                });
                            });
                        
      
                    });