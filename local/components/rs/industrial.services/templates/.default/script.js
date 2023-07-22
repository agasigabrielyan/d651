function unFavorIt(id){

  BX.ajax.runComponentAction('rs:industrial.services', 'unFavorIt', {
    mode: 'class',
    data: {
      'id' : id,
    },
  }).then(function (response) {

      let answer = JSON.parse(response.data);
      if(answer.status == 1){
          document.querySelector('.ds').outerHTML = answer.html;
		  insertSomeListeners();
      }
      else
          showNotyMessage(answer.error);

  }, function (response) {
      showNotyMessage(response.error);
  });
}

function favorIt(id){

  BX.ajax.runComponentAction('rs:industrial.services', 'favorIt', {
    mode: 'class',
    data: {
      'id' : id,
    },
  }).then(function (response) {

      let answer = JSON.parse(response.data);
      if(answer.status == 1){
          document.querySelector('.ds').outerHTML = answer.html;
		  insertSomeListeners();
      }else
          showNotyMessage(answer.error);

  }, function (response) {
      showNotyMessage(response.error);
  });

}

BX.ready(function () {
    let digitInput = document.querySelector('.digitalSearchSecond');

    let sectionsTitles = document.querySelectorAll('.dsServicesGroup__subTitle');
    digitInput.addEventListener('input', function () {
        let value = digitInput.value.toLowerCase();
        let digitItemsTitels = document.querySelectorAll('.dsServicesItem__title');
        if (value=='') {
            sectionsTitles.forEach(element => {
                element.style.display = 'block';
            })
            digitItemsTitels.forEach(element => {
                element.closest('.dsServicesItem').style.display = "flex";
            });
        }
        else {
            digitItemsTitels.forEach(element => {
                let currenttextContent = element.textContent.toLowerCase();
                if (currenttextContent.includes(value)) {
                    element.closest('.dsServicesItem').style.display = "flex";
                }
                else {
                    element.closest('.dsServicesItem').style.display = "none";
                }
            });
            sectionsTitles.forEach(element => {
                element.style.display = 'none';
            })
        }
    })
	insertSomeListeners();
})

function insertSomeListeners(){
	BX.UI.Hint.init(BX('container'));

    let hovers = Array.from(document.querySelector('.dsFavourite').querySelectorAll('lord-icon'));

    for(let i in hovers){
        hovers[i].onmouseover = function(evt){
            toggleText(evt)
        }

        hovers[i].onmouseout = function(evt){
            toggleText(evt)
        }
    }
}


function toggleText(evt){
    let text = evt.target.parentNode.querySelector('span');
    if(!text.classList.contains('visualizate'))
        text.classList.add('visualizate');
    else
        text.classList.remove('visualizate');
}

