class RSEntityComents {
    adCommentOpened = false;
    arParams;

    constructor(){
        this.arParams = window.entityCommentsParams;

        this.handleEditBtn();
        this.handleDeleteBtn();
        this.handleLoadBtn();

        this.correctFileInputs();
    }

    showCommentAdd(){
        this.adCommentOpened = true;
        document.querySelector('.entity-comments').querySelector('.ad-comment').classList.remove('hidden');
        document.querySelector('.entity-comments').querySelector('.new-comment .ui-btn').classList.add('hidden');
    }

    hideCommentAdd(){
        this.adCommentOpened = false;
        document.querySelector('.entity-comments').querySelector('.ad-comment').classList.add('hidden');
        document.querySelector('.entity-comments').querySelector('.new-comment .ui-btn').classList.remove('hidden');
    }

    hideCommentEdits(){
        let showedEditors = Array.from(document.querySelector('.entity-comments').querySelectorAll('.edit-comment:not(.hidden)'));
        for(let i in showedEditors){
            showedEditors[i].classList.add('hidden');
        }

        let hiddenComments = Array.from(document.querySelector('.entity-comments').querySelectorAll('.comment-info.hidden'));
        for(let i in hiddenComments){
            hiddenComments[i].classList.remove('hidden');
        }
    }


    handleEditBtn(){
        document.querySelector('.entity-comments').querySelector('.new-comment .ui-btn').addEventListener('click', () => this.showCommentAdd());
        document.body.addEventListener('click', (event) => this.onDocumentClick(event));

        let editOldCommentsBtn = Array.from(document.querySelector('.entity-comments').querySelectorAll('.edit'));
        for(let i in editOldCommentsBtn){
            editOldCommentsBtn[i].addEventListener('click', (evt) => this.showCommentEditArea(evt));
        }
    }
    handleDeleteBtn(){

        let editOldCommentsBtn = Array.from(document.querySelector('.entity-comments').querySelectorAll('.delete'));
        for(let i in editOldCommentsBtn){
            editOldCommentsBtn[i].addEventListener('click', (evt) => this.showCommentDeletePromt(evt));
        }
    }
    handleLoadBtn(){
        let btns = Array.from(document.querySelector('.entity-comments').querySelectorAll('.loadComment'));
        for(let i in btns){
            btns[i].addEventListener('click', (evt) => this.loadComment(evt));
        }
    }
    correctFileInputs(){
        let fileInputs = Array.from(document.querySelector('.entity-comments').querySelectorAll('input[type=file]')),
            files;
        for(let i in fileInputs){
            if(fileInputs[i].dataset.files)
                files = JSON.parse(fileInputs[i].dataset.files);
            else
                files = [];
            new BearFileInput(fileInputs[i], files);
        }
    }

    loadComment(evt){

        sendWorkForm(
            evt.target.closest('form'),
            Array.from(evt.target.closest('form').querySelectorAll('input')),
            'rs:entity.comments',
            'loadEntity',
            'window.entityCommentsList.refresh();'
        );
    }

    showCommentEditArea(evt){
        this.hideCommentEdits();
        evt.target.closest('.comment').querySelector('.comment-info').classList.add('hidden');
        evt.target.closest('.comment').querySelector('.edit-comment').classList.remove('hidden');
    }

    showCommentDeletePromt(evt){
        let self = this;
        let box = new BX.UI.Dialogs.MessageBox({
            message: BX.create({
                tag : 'div',
                attrs : { style : 'font: 14px "OpenSans", "Helvetica Neue", Helvetica, Arial, sans-serif;text-align: center;'},
                html : 'Подтвердите удаление комментария',
            }),
            buttons: BX.UI.Dialogs.MessageBoxButtons.OK_CANCEL,
            okCaption: "ОК",
            onOk: function(messageBox)
            {
                BX.ajax.runComponentAction('rs:entity.comments', 'deleteEntity', {
                    mode: 'class',
                    data: {
                        id: evt.target.closest('.delete').dataset.id,
                    },
                }).then(function (response) {
                    messageBox.close();
                    self.refresh();

                }, function (response) {
                    showNotyMessage("Произошла ошибка");
                });
                return false;
            },
            modal: true,
        })

        box.show();

    }

    refresh(){
        let self = this;
        BX.ajax.runComponentAction('rs:entity.comments', 'getComponentTemplateResult', {
            mode: 'class',
            data: {
                params: this.arParams,
            },
        }).then(function (response) {
            let parser = new DOMParser();
            let doc = parser.parseFromString(response.data, 'text/html');
            document.querySelector('.entity-comments').outerHTML = doc.querySelector('.entity-comments').outerHTML;

            let scripts = Array.from(doc.querySelectorAll('script'));
            for(let i in scripts)
                eval(scripts[i].innerHTML);

            self.handleEditBtn();
            self.handleDeleteBtn();
            self.handleLoadBtn();

            self.correctFileInputs();

        }, function (response) {
            showNotyMessage("Произошла ошибка");
        });
    }

    onDocumentClick(event){

        if(!event.target.closest('.edit-comment') && !event.target.closest('.edit') && !event.target.closest('.bxhtmled-popup'))
            this.hideCommentEdits();
    }

}



BX.ready(function(){
    window.entityCommentsList = new RSEntityComents();
})