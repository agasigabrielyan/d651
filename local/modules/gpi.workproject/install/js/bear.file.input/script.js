class BearFileInput{
    container;
    input;
    trigger;
    showListBtn;
    hideListBtn;
    fileList;
    newRows;
    oldRows;
    oldAddit;
    fileListStatus;

    constructor(fileInput, oldFileList, oldFileListAdditText='') {
        let self = this;
        let parent = fileInput.parentNode;

        let fileListAttrs = [
            BX.create({
                tag : 'div',
                props: {className: 'inputFilesList__title'},
                text: 'Загруженные файлы:',
            }),
            BX.create({
                tag : 'div',
                props: {className: 'inputFilesList__list',}
            }),
        ];


        fileListAttrs.push(
            BX.create({
                tag : 'div',
                props: {className: 'inputFilesList__past__title'},
                text: 'Загруженные раньше файлы:',
            }),
            BX.create({
                tag : 'div',
                props: {className: 'inputFilesList__list inputFilesList__past'},
            })
        );


        let content = BX.create({
            tag : 'div',
            props : {className : 'inputFiles'},
            html : fileInput.outerHTML,
            children: [
                BX.create({
                    tag : 'div',
                    props: {className: 'inputFiles__data'},
                    children: [
                        BX.create({
                            tag : 'div',
                            props: {className: 'inputFiles__trigger'},
                            html: fileInput.outerHTML,

                        }),
                        BX.create({
                            tag : 'div',
                            props: {className: 'inputFiles__link'},
                            text: 'Показать все файлы',
                        }),
                    ],
                }),
                BX.create({
                    tag: 'div',
                    props: {className: 'inputFilesList'},
                    children: fileListAttrs
                })
            ]
        });

        fileInput.outerHTML = content.outerHTML;

        this.oldAddit = oldFileListAdditText;
        this.container = parent.querySelector('.inputFiles');
        this.fileInput = this.container.querySelector('input');
        this.showListBtn = this.container.querySelector('.inputFiles__link');
        this.hideListBtn = this.container.querySelector('.inputFilesList__title');
        this.fileList = this.container.querySelector('.inputFilesList');
        this.newRows = this.container.querySelector('.inputFilesList__list');
        this.oldRows = this.container.querySelector('.inputFilesList__past');

        document.addEventListener('click', (event) => self.onDocumentClick(event));
        this.fileInput.addEventListener('change', (event) => self.onFileInputChange(event));
        this.hideListBtn.addEventListener('click', (event) => self.hideList(event));
        this.showListBtn.addEventListener('click', (event) => self.showList(event));

        this.fileInput.addEventListener('dragover', (event) => event.preventDefault());
        this.fileInput.addEventListener('dragenter', (event) => event.preventDefault());
        this.fileInput.addEventListener('dragenter', (event) => self.fileInput.parentNode.classList.add('drag'));
        this.fileInput.ondrop = (event) => self.onFileDrop(event);
        this.fileInput.draggable = true;

        if(oldFileList){
            this.setOldFileList(oldFileList);
        }else{
            this.container.querySelector('.inputFilesList__past__title').hidden = true;
            this.oldRows.hidden = true;
        }
        this.fileInput.style.display = 'block';
    }

    onFileInputChange(event){
        let files = Array.from(this.fileInput.files);

        let filesCount = files.length;

        if(filesCount>=1)
        {
            this.showListBtn.style.display = 'block';
        }
        let newBlock = true;
        let column;
        let counter = 0;

        if(!event.target.getAttribute('multiple')){
            let filesListColumns = Array.from(this.newRows.querySelectorAll('.inputFilesList__column'));
            for(let i in filesListColumns){
                filesListColumns[i].remove();
            }
        }


        for(let i =0; i<files.length; i++)
        {
            if(newBlock == true)
            {
                column = document.createElement('div');
                column.classList.add('inputFilesList__column');
                column.innerHTML = '';
                newBlock = false;
            }

            let thisFile = files[i];
            let filename = files[i].name;


            let row = document.createElement('div');
            row.classList.add('inputFilesList__row');

            let name = document.createElement('div');
            name.classList.add('name');
            name.innerHTML = filename;

            let remove = document.createElement('button');
            remove.classList.add('remove');

            remove.addEventListener('click',(event)=>{
                event.preventDefault();
                files.splice(files.indexOf(thisFile),1);
                row.remove();
                if(!files.length)
                {
                    filesShowLink.style.display = 'none';
                    filesListDiv.style.display = 'none';
                }
            });

            row.append(name);
            row.append(remove);

            column.append(row);

            counter++;
            if(counter % 7 == 0 || counter>=filesCount)
            {
                //пушим блок
                newBlock = true;
                this.newRows.append(column);
            }
        }
    }

    onFileDrop(event){
        event.preventDefault();

        this.fileInput.files = event.dataTransfer.files;

        const dT = new DataTransfer();
        for(let i in Array.from(event.dataTransfer.files)){
            dT.items.add(event.dataTransfer.files[i]);
        }
        this.fileInput.files = dT.files;

        this.fileInput.dispatchEvent( new Event('change'));
        this.fileInput.parentNode.classList.remove('drag')
    }

    onDocumentClick(event){
        if(!event.target.closest('.inputFiles') && this.fileListStatus == 'showed')
            this.hideList();
    }

    hideList(event){
        this.fileListStatus = 'hidden';
        this.fileList.style.display = 'none';
    }

    showList(event){
        this.fileListStatus = 'showed';
        this.fileList.style.display = 'block';
    }

    setOldFileList(fileList){

        let file;
        if(!fileList)
            return;

        this.oldRows.innerHTML = '';
        for(let i in fileList){
            file = fileList[i];
            if(!file)
                continue;

            this.oldRows.appendChild(BX.create("div", {
                attrs : {className: 'inputFilesList__row'},
                dataset:{fileId : file[this.oldAddit+'ID'], name: file[this.oldAddit+'ORIGINAL_NAME']},
                children: [
                    BX.create("a", {
                        attrs : {className: 'name', href: "/upload/"+file[this.SUBDIR]+"/"+file[this.oldAddit+'FILE_NAME'], target: '_blank'},
                        text : file[this.oldAddit+'ORIGINAL_NAME'],
                    }),
                    BX.create("button", {
                        attrs : {className: 'remove'},
                        events: {
                            click : function(event){
                                event.target.parentNode.remove();
                            }
                        }
                    })
                ]
            }))
        }

        this.showListBtn.style.display = 'block';
    }

    showFileDialog(event){
        this.fileInput.click();
    }

}