<template>

  <div class="todo-container">

    <form v-on:submit.prevent="addList();" class="mt-2 mb-2">

      <p class="inline">Lisää uusi tehtävälista: </p>

      <input type="text" placeholder="Listan nimi" class="border border-black" v-model="newListName" required />

      <button class="bg-green-500 pr-2 pl-2 text-white font-bold" type="submit">+</button>

    </form>

    <hr class="mb-2">

    <div class="todo-grid grid grid-cols-4 gap-4">

      <div v-for="(list, lindex) in lists" class="border p-3 shadow-sm">

        <div class="list-header mb-6">

        <h3 class="font-bold inline">{{ list.list_name }}</h3>

        <button type="button" class="bg-red-500 p-2 inline-block float-right" @click="deleteList(lindex,list.list_id);">Poista lista</button>

      </div>

        <hr>

        <ol v-for="(task, index) in list.tasks" class="mt-2">

          <li class="p-2 bg-gray-300 h-auto">{{ index+1 }}. <p class="inline" :id="`taskContent${task.task_id}`">{{ task.task_content }}</p>

            <input type="text" :id="`editedTaskContent${task.task_id}`" class="inline-block hidden" />

            <button type="button" class="inline-block float-right bg-gray-500 pl-1 pr-1 font-bold" @click="deleteTask(index,list.list_id);">&times;</button>

            <button type="button" class="inline-block float-right bg-gray-500 pl-1 pr-1 mr-1" :id="`editTaskButton${task.task_id}`" @click="editTask(task.task_id);">✎</button>

            <button type="button" class="inline-block float-right bg-gray-500 pl-1 pr-1 mr-1 hidden" :id="`saveTaskButton${task.task_id}`" @click="saveEditedTask(index,lindex,task.task_id);">💾</button>

          </li>

        </ol>

            <form v-on:submit.prevent="addTask(lindex,list.list_id);" class="flex flex-row content-center inline">

            <input type="text" class="mt-2 border border-black w-auto" placeholder="Lisää tehtävä" ref="newTaskContent" required />

            <button class="p-2 mt-2 bg-green-400 mt-1 inline-block float-right w-full">Lisää tehtävä</button>

          </form>

      </div>

    </div>

  </div>

</template>

<script>

/**
 * Listan constructori
 * @param       {number} lid - Listan id
 * @param       {number} lname - Listan nimi
 * @param       {number} lcreator - Listan tekijä
 * @param       {Promise} ltasks - Listan tehtävät
 * @constructor
 */
function List(lid, lname, lcreator, ltasks) {

  this.list_id = lid;

  this.list_name = lname;

  this.creator_name = lcreator;

  this.tasks = [];

  ltasks.then((result) => {

    result['data'].forEach((task_data, i) => {

      this.tasks.push({task_id: task_data.id, task_content: task_data.task});

    });

   }).catch((error) => { console.log(error); })

}

    export default {

      data() {

        return {

          newListName: null,

          lists: [

            //Esimerkki tehtävälistan datasta:
            //{list_id: 1, list_name: 'Koodaa robotti', creator_name: 'Makke', tasks: [{task_id: 1, task_content:'koodaa 1'},{task_id: 2, task_content:'koodaa 2'},{task_id: 3, task_content:'koodaa 3'}]},

          ]

        }

      },

      methods: {

        /**
         * Hakee käyttäjän tehtävälistat tietokannasta
         */
        getLists() {

          //Haetaan rajapinnan kautta kaikki käyttäjän luomat tehtävälistat
          const data = window.axios.get('/listapi/lists');

          //Käydään haettu datapromise läpi
          data.then((result) => {

            result['data'].forEach((list, i) => {

              //Haetaan jokaiseen tehtävälistaan tehtävät tietokannasta
              let list_tasks = window.axios.get(`/listapi/lists/${list.id}`);

              //Lisätään tehtävälistat käyttöliittymään muodostaen ne List-constructorin avulla
              this.lists.push(new List(list.id, list.list_name, list.creator_name, list_tasks));

            });

          }).catch((error) => { console.log(error); })

        },

        /**
         * Poistaa tehtävän listasta
         * @param  {number} index - Tehtävän id
         * @param  {number} lid - Listan id
         */
        deleteTask(index,lid) {

          //Haetaan lista josta tehtävä poistetaan list_id:n perusteella
          let list = this.lists.find(list => list.list_id == lid);

          //Haetaan poistettavan tehtävän id tietokannasta poistoa varten
          const task_id = list.tasks[index].task_id;

          //Poistetaan tehtävä halutusta listasta käyttöliittymän puolella
          list.tasks.splice(index,1);

          //Poistetaan tehtävä tietokannasta rajapinnan kautta
          window.axios.delete(`/taskapi/tasks/${task_id}`,{data:{listid: lid}});

        },

        /**
         * Lisää tehtävän valittuun listaan
         * @param {number} index - Listan index käyttöliittymässä
         * @param {number} lid - Listan tietokannassa oleva id
         */
        addTask(index,lid) {

          //Haetaan lista johon tehtävä lisätään list_id:n perusteella
          const list = this.lists.find(list => list.list_id == lid);

          //Haetaan uuden tehtävän sisältö newTaskContent-inputista
          const newtask = this.$refs.newTaskContent[index].value;

          //Lisätään tehtävä tietokantaan. Parametreinä toimii listan id ja tehtävän sisältö
          window.axios.post('/taskapi/tasks', {

            listid: lid,

            newtaskcontent: newtask

          }).then((response) => {

            const latestid = response['data'][1];

            //Lisätään käyttöliittymän puolella listaan newTaskContent-inputista haettu arvo
            list.tasks.push({task_id: latestid, task_content: newtask});

          });

          //Tyhjennetään newTaskContent-input
          this.$refs.newTaskContent[index].value = null;

        },

        /**
         * Poistetaan lista
         * @param  {number} index - Listan index
         * @param  {number} lid - Listan id
         */
        deleteList(index,lid) {

          //Poistetaan lista ja sen tehtävät tietokannasta rajapinnan kautta listan id:n perusteella
          window.axios.delete(`/listapi/lists/${lid}`)
          .then((result) => {

            //Poistetaan lista käyttöliittymästä
            this.lists.splice(index,1);

          }).catch((error) => { console.log(error); });

        },

        /**
         * Luodaan uusi lista
         */
        addList() {

          //Haetaan käyttäjän tiedot
          const userdata = window.axios.get('/userapi/user');

          //Käydään käyttäjän tiedot läpi
          userdata.then((response) => {

            //Käyttäjänimi
            const creator = response['data'].username;

            //Lisätään lista tietokantaan rajapinnan kautta
            window.axios.post('/listapi/lists', {

              listname: this.newListName

            }).then((response) => {

              //Lisätyn listan id
              const latestid = response['data'][1];

              //Lisätään lista käyttöliittymän puolelle
              this.lists.push({ list_id: latestid, list_name: this.newListName, creator_name: creator, tasks: []});

              this.newListName = null;

            }).catch((error) => { console.log(error); });

           }).catch((error) => { console.log(error); })

        },

        /**
         * Asetetaan tehtävän vanha arvo muokattavaksi
         * @param  {number} index - Tehtävän index
         * @param  {number} listindex - Listan index
         * @param  {number} taskid - Tehtävän id
         */
        editTask(taskid) {

          //Tehtävän muokattu sisältö
          let editedtaskcontent = document.getElementById('editedTaskContent'+taskid);

          //Keskitetään tekstikursori inputtiin
          editedtaskcontent.focus();

          //Tehtävän nykyinen sisältö
          let taskcontent = document.getElementById('taskContent'+taskid);

          //Asetetaan muokattavaksi nykyinen arvo
          editedtaskcontent.value = taskcontent.innerText;

          //Piilotetaan muokkauspainike ja näytetään tehtävän tallennuspainike
          this.toggleTaskEditOptions(taskid);

        },

        /**
         * Tallennetaan tehtävän uusi arvo
         * @param  {number} index - Tehtävän index
         * @param  {number} listindex - Listan index
         * @param  {number} taskid - Tehtävän id
         */
        saveEditedTask(index,listindex,taskid) {

          //Haetaan tehtävä listasta jossa se on
          let task = this.lists[listindex].tasks[index];

          //Tehtävän muokattu sisältö
          let editedtaskcontent = document.getElementById('editedTaskContent'+taskid);

          //Jos tehtävän vanha sisältö eroaa uudesta, päivitetään tehtävä
          if(task.task_content!=editedtaskcontent.value) {

            window.axios.put(`/taskapi/tasks/${taskid}`,{

              newtaskcontent: editedtaskcontent.value,

              listid: this.lists[listindex].list_id

            }).then((response) => {

              //Asetetaan uusi arvo
              this.$set(task, 'task_content', editedtaskcontent.value);

              //Vaihdetaan tehtävän muokkauspainike takaisin näkyviin
              this.toggleTaskEditOptions(taskid);

            }).catch((error) => { console.log(error); });

            //Jos tehtävä sisältö ei muutu, piilotetaan vain tehtävän muokkausominaisuudet
          } else {

            //Vaihdetaan tehtävän muokkauspainike takaisin näkyviin
            this.toggleTaskEditOptions(taskid);

          }

        },

        /**
         * Muutetaan tehtävän tallennus- ja muokkauspainikkeiden näkyvyyttä annetun tehtävän id:n perusteella
         * @param {number} taskid - Tehtävän id
         */
        toggleTaskEditOptions(taskid) {

          //Tehtävän sisältö
          let taskcontent = document.getElementById('taskContent'+taskid);

          //Tehtävän muokkauspainike
          let edittaskbutton = document.getElementById('editTaskButton'+taskid);

          //Tehtävän tallennuspainike
          let savetaskbutton = document.getElementById('saveTaskButton'+taskid);

          //Tehtävän muokkausinput
          let editedtaskcontent = document.getElementById('editedTaskContent'+taskid);

          //Muutetaan tehtävän sisällön sekä tallennus- ja muokkauspainikkeiden näkyvyyttä
          taskcontent.classList.toggle('hidden');
          edittaskbutton.classList.toggle('hidden');
          savetaskbutton.classList.toggle('hidden');
          editedtaskcontent.classList.toggle('hidden');

        }

      },

      created() {

        //Kun käyttöliittymä on luotu, haetaan tietokannasta käyttäjän tehtävälistat
        this.getLists();

      }

    }
</script>
