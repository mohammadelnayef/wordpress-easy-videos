document.addEventListener("DOMContentLoaded", function() {

    let easyVideos = {
       
        init(){
            this.changeAddNewButton();
        },

        isEasyVideosAdminPage(){
            if(window.location.href.indexOf('/wp-admin/edit.php?post_type=easy-videos') > -1){
                return true
            }
            else{
                return false
            }
        },

        changeAddNewButton(){
            if(this.isEasyVideosAdminPage()){
               let element = document.querySelector('.page-title-action')
               console.log(element.textContent = 'Insert Video')
            }
        }
    }

    easyVideos.init()
});

