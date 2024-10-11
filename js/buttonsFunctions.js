const associate = () => {
  let projectId = document.getElementById("gitlabProject").value;
  let categoryId = document.getElementById("glpiCategory").value;

  jQuery
    .ajax({
      type: "POST",
      url: "../ajax/categoryProject.php",
      data: {
        project_id: projectId,
        category_id: categoryId,
        modo: 1,
      },
    })
    .success(() => {
      window.open("../front/categoriesProjects.php", "_self");
    })
    .fail(() => {
      return false;
    });
};





const createIssue = (
  ticketId,
  ticketName,
  ticketContent,
  ticketDueDate,
  ticketType,
  ticketLabel,
  ticketLink,
  $message
) => {
  let formattedTicketContent = formatTicketContent(ticketContent);
  formattedTicketContent += ticketLink;
  let selectedProject = document.getElementById("project_id").value;
  let selectedCategory = document.getElementsByName("itilcategories_id").value;

  jQuery
    .ajax({
      type: "POST",
      url: "../plugins/gitlabintegration/ajax/issue.php",
      data: {
        selectedProject: selectedProject,
        selectedCategory: selectedCategory,
        ticketId: ticketId,
        ticketName: ticketName,
        ticketContent: formattedTicketContent,
        ticketDueDate: ticketDueDate,
        ticketType: ticketType,
        ticketLabel: ticketLabel,
        method:"create"
      },
    })
    .success((res) => {

      if(res.message){
        alert(res.message);
      }else{
        alert("Ocorreu um erro!");
      }

      setTimeout(() =>{window.location.reload();},5000)
    })
    .fail(() => {
      alert("Ocorreu um erro!");
      setTimeout(() =>{window.location.reload();},5000)
    });
};

const setDefault = () => {
  let defaultProject = document.getElementById("default_project").value;

  jQuery
    .ajax({
      type: "POST",
      url: "../ajax/categoryProject.php",
      data: {
        project_id: defaultProject,
        modo: 2,
      },
    })
    .success(() => {
      window.open("../front/defaultProject.php", "_self");
    });
};

const removeDefault = (id) => {
  jQuery
    .ajax({
      type: "POST",
      url: "../ajax/categoryProject.php",
      data: {
        modo: 3,
        project_id: id,
      },
    })
    .success(() => location.reload())
    .fail(() => false);
};

const formatTicketContent = (ticketContent) => {
  ticketContent = ticketContent.replace(new RegExp("<p>", "g"), "");
  ticketContent = ticketContent.replace(new RegExp("</p>", "g"), "<br>");

  ticketContent = ticketContent.replace(new RegExp("<strong>", "g"), "**");
  ticketContent = ticketContent.replace(new RegExp("</strong>", "g"), "**");

  ticketContent = ticketContent.replace(new RegExp("<em>", "g"), "*");
  ticketContent = ticketContent.replace(new RegExp("</em>", "g"), "*");

  if (ticketContent.indexOf("<ul>")) {
    let lista = ticketContent.substring(
      ticketContent.indexOf("<ul>"),
      ticketContent.indexOf("</ul>") + 5
    );
    let newLista = lista.replace(new RegExp("<li>", "g"), "* ");
    newLista = newLista.replace(new RegExp("</li>", "g"), "<br>");
    ticketContent = ticketContent.replace(new RegExp(lista, "g"), newLista);
  }

  ticketContent = ticketContent.replace(new RegExp("<ul>", "g"), "");
  ticketContent = ticketContent.replace(new RegExp("</ul>", "g"), "<br>");

  if (ticketContent.indexOf("<ol>")) {
    let lista = ticketContent.substring(
      ticketContent.indexOf("<ol>"),
      ticketContent.indexOf("</ol>") + 5
    );

    let pos = 0;
    let contagem = 0;

    while (true) {
      pos = lista.indexOf("<li>", pos + 1);
      if (pos < 0) break;
      contagem++;
    }

    let item, newItem;
    let newLista = lista;

    for (let x = 1; x <= contagem; x++) {
      pos = newLista.indexOf("<li>");
      item = newLista.substring(pos, newLista.indexOf("</li>") + 5);
      newItem = item.replace(new RegExp("<li>", "g"), x + ". ");
      newItem = newItem.replace(new RegExp("</li>", "g"), "<br>");
      newLista = newLista.replace(new RegExp(item, "g"), newItem);
    }

    ticketContent = ticketContent.replace(new RegExp(lista, "g"), newLista);
  }

  ticketContent = ticketContent.replace(new RegExp("<ol>", "g"), "");
  ticketContent = ticketContent.replace(new RegExp("</ol>", "g"), "<br>");

  return ticketContent;
};

const setSelectedProject = (dropdown, selectedProject) => {
  let dropdownProject = document.getElementById("dropdown_project" + dropdown);

  if (selectedProject) {
    for (var i, j = 0; (i = dropdownProject.options[j]); j++) {
      if (i.value == selectedProject) {
        dropdownProject.selectedIndex = j;
        break;
      }
    }
    let span = document.getElementById(
      "select2-dropdown_project" + dropdown + "-container"
    );
    span.textContent =
      dropdownProject.options[dropdownProject.selectedIndex].text;
    span.title = dropdownProject.options[dropdownProject.selectedIndex].text;
  }
};

const addProfile = (dropdown, userId) => {
  let dropdownProfile = document.getElementById(
    "dropdown__profiles_id" + dropdown
  );
  let newProfileSelected =
    dropdownProfile.options[dropdownProfile.selectedIndex].value;

  jQuery
    .ajax({
      type: "POST",
      url: "../ajax/profile.php",
      data: {
        profileId: newProfileSelected,
        userId: userId,
        method: "create"
      },
    })
    .success(function (resp) {
      
      alert(resp.message);
      
      if(!resp.success){
        return;
      }
  
      setTimeout(()=>{
        window.open("../front/profiles.php", "_self");
        },3000)

    })
    .fail(function () {
        alert("Ocorreu um erro!");
      return false;
    });
};

const setClickCheckAll = (checkboxName, principal) => {
  checkboxName = "checkAll_" + checkboxName;
  let checkbox = document.getElementsByName(checkboxName);
  checkbox[0].addEventListener(
    "change",
    function () {
      if (principal) {
        checkAllBoxes(checkboxName, checkbox[0].checked);
      } else {
        changeCheckAll();
      }
    },
    false
  );
};

const checkAllBoxes = (checkboxName, selected) => {
  let inputs = document.getElementsByTagName("input");
  for (let i = 0; i < inputs.length; i++) {
    if (inputs[i].type == "checkbox") {
      if (inputs[i].name != checkboxName) {
        inputs[i].checked = selected;
      }
    }
  }
};

const changeCheckAll = () => {
  let inputsData = document
    .getElementById("data")
    .getElementsByTagName("input");

  let i;
  let countSelected = 0;
  for (i = 0; i < inputsData.length; i++) {
    if (inputsData[i].type == "checkbox") {
      if (inputsData[i].checked) {
        countSelected++;
      }
    }
  }

  let inputTop = document
    .getElementById("principal_1")
    .getElementsByTagName("input");
  let inputBotton = document
    .getElementById("principal_2")
    .getElementsByTagName("input");

  if (countSelected == inputsData.length) {
    changeCheckInput(inputTop, true);
    changeCheckInput(inputBotton, true);
  } else {
    changeCheckInput(inputTop, false);
    changeCheckInput(inputBotton, false);
  }
};

const changeCheckInput = (inputArray, checked) => {
  for (i = 0; i < inputArray.length; i++) {
    if (inputArray[i].type == "checkbox") {
      inputArray[i].checked = checked;
    }
  }
};

const openActions = () => {
  countCheckSelected();
  let div = document.getElementById("favDialog");
  // $(div).dialog();
};

const countCheckSelected = () => {
  let inputsData = document
    .getElementById("data")
    .getElementsByTagName("input");

  let countSelected = 0;
  for (i = 0; i < inputsData.length; i++) {
    if (inputsData[i].type == "checkbox") {
      if (inputsData[i].checked) {
        countSelected++;
      }
    }
  }
  if (countSelected == 0) {
    document.getElementById("no_information").style.visibility = "visible";
    document.getElementById("options_to_select").style.visibility = "hidden";
    document.getElementById("button_confirm_action").style.visibility =
      "hidden";
  } else {
    document.getElementById("no_information").style.visibility = "hidden";
    document.getElementById("options_to_select").style.visibility = "visible";
    document.getElementById("button_confirm_action").style.visibility =
      "visible";
  }
};

const removeAssociation = (projectId, categoryId) => {
  jQuery
    .ajax({
      type: "POST",
      url: "../ajax/categoryProject.php",
      data: {
        modo: 0,
        project_id: projectId,
        category_id: categoryId,
      },
    })
    .success(() => location.reload())
    .fail(() => false);
};


window.onload = function(){


  function idsChecked(){
    let ids = []
    checksGitLab.forEach(e =>{
         if(e.checked){
            ids.push(e.getAttribute("data-id"));
         }
        
    })
    return ids;
  }

  const btnExcluirProfile =   document.getElementById("btn_excluir_profile");

  if(btnExcluirProfile){
      btnExcluirProfile.addEventListener("click",()=>{
        let idsCheckedValues =  idsChecked();
        if(!idsCheckedValues.length){
            alert("Para excluir selecione um profile");
            return;
        }
  
        jQuery
        .ajax({
          type: "POST",
          url: "../ajax/profile.php",
          data: {
            method: "delete",
            ids: idsCheckedValues,
          },
        })
        .success(function (resp) {
            alert(resp.message);
  
            if(resp.success){
            setTimeout(()=>{
              window.open("../front/profiles.php", "_self");
              },3000)
            }
        
        })
        .fail(function () {
          return false;
        });
  
    });
 
  }


  const checksGitLab = document.querySelectorAll(".gitlab_profile_check");
  const checkAllGitLab = document.querySelector("input[name=gitlab_profile_check_all]");

  if(checkAllGitLab){
    checkAllGitLab.addEventListener("change",(checkAll) =>{
      checksGitLab.forEach(e => {
             e.checked =  checkAll.target.checked;
      });
  
    });
  }



  
 
}

