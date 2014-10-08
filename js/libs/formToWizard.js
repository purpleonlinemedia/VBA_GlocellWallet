/* Created by jankoatwarpspeed.com */

(function($) {
    $.fn.formToWizard = function(options) {
        options = $.extend({  
            submitButton: ''  
        }, options); 
        
        var element = this;

        var steps = $(element).find("fieldset");
        var count = steps.size();
        var submmitButtonName = "#" + options.submitButton;
        $(submmitButtonName).hide();
        
        
        //if submit button is clicked
        $("form").submit(function(){
            var landmarkID = $("#Name").val();
           
            $.ajax({
                    type: 'POST',
                    data: '&lid='+landmarkID,
                    //change the url for your project
                    url: 'http://localhost:8383/directory/process.php',
   
                success: function(data){
                        alert('Your comment was successfully added');
                    },
                    error: function(){
                        
                        alert('There was an error adding your comment');
                    }
                });
                 return false;

        });
        
        // 2
        $(element).before("<ul id='steps'></ul>");

        steps.each(function(i) {
            $(this).wrap("<div id='step" + i + "'></div>");
            $(this).append("<p id='step" + i + "commands'></p>");

            // 2
            var name = $(this).find("legend").html();
            //$("#steps").append("<li id='stepDesc" + i + "'>Step " + (i + 1) + "<span>" + name + "</span></li>");
            $("#steps").append("<li id='stepDesc" + i + "'><font size='2'>Step " + (i + 1) + "</font></li>");

            if (i == 0) {
                createNextButton(i);
                selectStep(i);
            }
            else if (i == count - 1) {
                $("#step" + i).hide();
                createPrevButton(i);
            }
            else {
                $("#step" + i).hide();
                createPrevButton(i);
                createNextButton(i);
            }
        });

        function createPrevButton(i) {
            var stepName = "step" + i;
            //$("#" + stepName + "commands").append("<a href='#' id='" + stepName + "Prev' class='prev'>< Back</a>");
            $("#" + stepName + "commands").append("<input type='button' value='Back' id='"+stepName+"Prev' class='prev'>");

            $("#" + stepName + "Prev").bind("click", function(e) {
                $("#" + stepName).hide();
                $("#step" + (i - 1)).show();
                $(submmitButtonName).hide();
                selectStep(i - 1);
            });
        }

        function createNextButton(i) {
            var stepName = "step" + i;
            //$("#" + stepName + "commands").append("<a href='#' id='" + stepName + "Next' class='next'>Next ></a>");
            $("#" + stepName + "commands").append("<input type='button' value='Next' id='"+stepName+"Next' class='next'>");

            $("#" + stepName + "Next").bind("click", function(e) 
            {
                var returnValue = methodCalls.verifyStep(stepName);
                if(returnValue === 1)
                {
                    $("#" + stepName).hide();
                    $("#step" + (i + 1)).show();
                    if (i + 2 == count)
                        $(submmitButtonName).show();
                    selectStep(i + 1);
                    
                    if(stepName === 'step3')
                    {                        
                        window.scrollTo(0,150);  
                    }else{
                        window.scrollTo(0,0);   
                    }                    
                }
            });
        }

        function selectStep(i) {
            $("#steps li").removeClass("current");
            $("#stepDesc" + i).addClass("current");
        }
    }
})(jQuery); 
$(document).ready(function(){

});