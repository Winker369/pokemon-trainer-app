<template>
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-md-8">
        <h1>Choose Your Pokemons</h1>
        <h3>Like or hate pokemons up to 3 only! And choose your favorite!</h3>
        <ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
          <li class="nav-item" role="presentation">
            <button class="nav-link active" id="pills-pokemons-tab" data-bs-toggle="pill" data-bs-target="#pills-pokemons" type="button" role="tab" aria-controls="pills-pokemons" aria-selected="true">Pokemons</button>
          </li>
          <li class="nav-item" role="presentation">
            <button class="nav-link" id="pills-liked-tab" data-bs-toggle="pill" data-bs-target="#pills-liked" type="button" role="tab" aria-controls="pills-liked" aria-selected="false">Liked</button>
          </li>
          <li class="nav-item" role="presentation">
            <button class="nav-link" id="pills-hated-tab" data-bs-toggle="pill" data-bs-target="#pills-hated" type="button" role="tab" aria-controls="pills-hated" aria-selected="false">Hated</button>
          </li>
        </ul>
        <div class="tab-content" id="pills-tabContent">
          <div class="tab-pane fade show active" id="pills-pokemons" role="tabpanel" aria-labelledby="pills-pokemons-tab">
            <div class="list-group-wrapper">
              <transition name="fade">
                <div class="spinner-border text-success loading" style="width: 3rem; height: 3rem;" role="status" v-show="loading">
                  <!-- <span class="sr-only">Loading...</span> -->
                </div>
              </transition>
              <div class="list-group" id="infinite-list">
                <a v-for="pokemon, pokemonKey in pokemons" href="#" class="list-group-item list-group-item-action bg-color" aria-current="true">
                  <div class="d-flex w-100 justify-content-between">
                    <h3 class="mb-1">#{{ pokemon.id }} {{ pokemon.name }}</h3>
                    <div>
                      <button
                        v-if="pokemon.favorite"
                        class="btn btn-success"
                        style="margin-right: 10px;"
                        @click="unfavorite(pokemon, pokemonKey)">
                        Favorite
                      </button>
                      <button
                        v-else
                        class="btn btn-outline-success"
                        style="margin-right: 10px;"
                        @click="favorite(pokemon, pokemonKey)">
                        Favorite
                      </button>
                      <button
                        v-if="pokemon.reaction == 'Like'"
                        class="btn btn-primary"
                        style="margin-right: 10px;"
                        @click="unlike(pokemon, pokemonKey)">
                        Like
                      </button>
                      <button
                        v-else
                        class="btn btn-outline-primary"
                        style="margin-right: 10px;"
                        @click="like(pokemon, pokemonKey)">
                        Like
                      </button>
                      <button
                        v-if="pokemon.reaction == 'Hate'"
                        class="btn btn-danger"
                        style="margin-right: 10px;"
                        @click="unhate(pokemon, pokemonKey)">
                        Hate
                      </button>
                      <button
                        v-else
                        class="btn btn-outline-danger"
                        style="margin-right: 10px;"
                        @click="hate(pokemon, pokemonKey)">
                        Hate
                      </button>
                    </div>
                  </div>
                </a>
              </div>
            </div>
          </div>
          <div class="tab-pane fade" id="pills-liked" role="tabpanel" aria-labelledby="pills-liked-tab">
            <div class="list-group">
              <a v-for="pokemon, pokemonKey in liked_pokemons" href="#" class="list-group-item list-group-item-action bg-color" aria-current="true">
                <div class="d-flex w-100 justify-content-between">
                  <h3 class="mb-1">#{{ pokemon.id }} {{ pokemon.name }}</h3>
                  <div>
                    <button
                      v-if="pokemon.favorite"
                      class="btn btn-success"
                      style="margin-right: 10px;"
                      @click="unfavorite(pokemon, pokemonKey)">
                      Favorite
                    </button>
                    <button
                      v-else
                      class="btn btn-outline-success"
                      style="margin-right: 10px;"
                      @click="favorite(pokemon, pokemonKey)">
                      Favorite
                    </button>
                    <button
                      v-if="pokemon.reaction == 'Like'"
                      class="btn btn-primary"
                      style="margin-right: 10px;"
                      @click="unlike(pokemon, pokemonKey)">
                      Like
                    </button>
                    <button
                      v-else
                      class="btn btn-outline-primary"
                      style="margin-right: 10px;"
                      @click="like(pokemon, pokemonKey)">
                      Like
                    </button>
                    <button
                      v-if="pokemon.reaction == 'Hate'"
                      class="btn btn-danger"
                      style="margin-right: 10px;"
                      @click="unhate(pokemon, pokemonKey)">
                      Hate
                    </button>
                    <button
                      v-else
                      class="btn btn-outline-danger"
                      style="margin-right: 10px;"
                      @click="hate(pokemon, pokemonKey)">
                      Hate
                    </button>
                  </div>
                </div>
              </a>
            </div>
          </div>
          <div class="tab-pane fade" id="pills-hated" role="tabpanel" aria-labelledby="pills-hated-tab">
            <div class="list-group">
              <a v-for="pokemon, pokemonKey in hated_pokemons" href="#" class="list-group-item list-group-item-action bg-color" aria-current="true">
                <div class="d-flex w-100 justify-content-between">
                  <h3 class="mb-1">#{{ pokemon.id }} {{ pokemon.name }}</h3>
                  <div>
                    <button
                      v-if="pokemon.favorite"
                      class="btn btn-success"
                      style="margin-right: 10px;"
                      @click="unfavorite(pokemon, pokemonKey)">
                      Favorite
                    </button>
                    <button
                      v-else
                      class="btn btn-outline-success"
                      style="margin-right: 10px;"
                      @click="favorite(pokemon, pokemonKey)">
                      Favorite
                    </button>
                    <button
                      v-if="pokemon.reaction == 'Like'"
                      class="btn btn-primary"
                      style="margin-right: 10px;"
                      @click="unlike(pokemon, pokemonKey)">
                      Like
                    </button>
                    <button
                      v-else
                      class="btn btn-outline-primary"
                      style="margin-right: 10px;"
                      @click="like(pokemon, pokemonKey)">
                      Like
                    </button>
                    <button
                      v-if="pokemon.reaction == 'Hate'"
                      class="btn btn-danger"
                      style="margin-right: 10px;"
                      @click="unhate(pokemon, pokemonKey)">
                      Hate
                    </button>
                    <button
                      v-else
                      class="btn btn-outline-danger"
                      style="margin-right: 10px;"
                      @click="hate(pokemon, pokemonKey)">
                      Hate
                    </button>
                  </div>
                </div>
              </a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  props: ['resource_list'],

  created() {
      console.log(this.resource_list)
      this.pokemons = this.resource_list.results
      this.liked_pokemons = this.resource_list.liked_pokemons
      this.hated_pokemons = this.resource_list.hated_pokemons
  },

  mounted () {
    // Detect when scrolled to bottom.
    let list_element = document.querySelector('#infinite-list');

    list_element.addEventListener('scroll', e => {
      if(list_element.scrollTop + list_element.clientHeight >= list_element.scrollHeight) {
        this.loadMore();
      }
    })
  },

  data() {
    return {
      loading: false,
      pokemons: [],
      liked_pokemons: [],
      hated_pokemons: []
    }
  },

  methods: {
    loadMore () {
      this.loading = true

      setTimeout(e => {
        // console.log('Load More')
        this.loading = false
      }, 200);

      // axios
      //   .get('pokemon', {
      //     params: {
      //         url: this.resource_list.next,
      //     },
      //   })
      //   .then((res) => {
      //     let res_data = res.data
      //     // Push new pokemons to the list
      //     for (const [key, value] of Object.entries(res_data.body.data.results)) {
      //       this.pokemons.push(value)
      //     }
      //     // Update next url in resource list
      //     this.resource_list.next = res_data.body.data.next
      //     // Update like and hate list
      //     this.liked_pokemons = res_data.body.data.liked_pokemons
      //     this.hated_pokemons = res_data.body.data.hated_pokemons
      //   })
      //   .catch((error) => {
      //   })
      //   .finally(() => {
      //     this.loading = false
      //   })
    },

    favorite(pokemon, pokemonKey) {
      axios
        .post('pokemon/favorite', {
            name: pokemon.name,
            url: pokemon.url,
          }
        )
        .then((res) => {
          let res_data = res.data
          alert(res_data.body.message)
          // Update list data
          this.updateLists(pokemon, res_data)
        })
        .catch((error) => {
          let response = JSON.parse(error.request.responseText)
          alert(response.body.message)
        })
        .finally(() => {
        })
    },

    unfavorite(pokemon, pokemonKey) {
      let url = pokemon.url.split('/')
      let pokemon_id = url[url.length - 2]

      axios
        .delete('pokemon/' + pokemon_id + '/unfavorite', {
            name: pokemon.name,
            url: pokemon.url,
          }
        )
        .then((res) => {
          let res_data = res.data
          alert(res_data.body.message)
          // Update list data
          this.updateLists(pokemon, res_data)
        })
        .catch((error) => {
          let response = JSON.parse(error.request.responseText)
          alert(response.body.message)
        })
        .finally(() => {
        })
    },

    like(pokemon, pokemonKey) {
      axios
        .post('pokemon/like', {
            name: pokemon.name,
            url: pokemon.url,
          }
        )
        .then((res) => {
          let res_data = res.data
          alert(res_data.body.message)
          // Update list data
          this.updateLists(pokemon, res_data)
        })
        .catch((error) => {
          let response = JSON.parse(error.request.responseText)
          alert(response.body.message)
        })
        .finally(() => {
        })
    },

    unlike(pokemon, pokemonKey) {
      let url = pokemon.url.split('/')
      let pokemon_id = url[url.length - 2]

      axios
        .delete('pokemon/' + pokemon_id + '/unlike', {
            name: pokemon.name,
            url: pokemon.url,
          }
        )
        .then((res) => {
          let res_data = res.data
          alert(res_data.body.message)
          // Update list data
          this.updateLists(pokemon, res_data)
        })
        .catch((error) => {
          let response = JSON.parse(error.request.responseText)
          alert(response.body.message)
        })
        .finally(() => {
        })
    },

    hate(pokemon, pokemonKey) {
      axios
        .post('pokemon/hate', {
            name: pokemon.name,
            url: pokemon.url,
          }
        )
        .then((res) => {
          let res_data = res.data
          alert(res_data.body.message)
          // Update list data
          this.updateLists(pokemon, res_data)
        })
        .catch((error) => {
          let response = JSON.parse(error.request.responseText)
          alert(response.body.message)
        })
        .finally(() => {
        })
    },

    unhate(pokemon, pokemonKey) {
      let url = pokemon.url.split('/')
      let pokemon_id = url[url.length - 2]

      axios
        .delete('pokemon/' + pokemon_id + '/unhate', {
            name: pokemon.name,
            url: pokemon.url,
          }
        )
        .then((res) => {
          let res_data = res.data
          alert(res_data.body.message)
          // Update list data
          this.updateLists(pokemon, res_data)
        })
        .catch((error) => {
          let response = JSON.parse(error.request.responseText)
          alert(response.body.message)
        })
        .finally(() => {
        })
    },

    updateLists(pokemon, response) {
      // Check if there is a previous favorite
      let previousPokemonKey = this.pokemons.findIndex(main_list_pokemon => main_list_pokemon.favorite)
      // Find index in main list
      let pokemonKey = this.pokemons.findIndex(main_list_pokemon => main_list_pokemon.name == pokemon.name)

      if (previousPokemonKey >= 0 && response.body.data.hasOwnProperty('favorite')) {
        this.pokemons[previousPokemonKey]['favorite'] = 0
      }

      if (response.body.data.hasOwnProperty('favorite')) {
        // Update list data
        this.pokemons[pokemonKey]['favorite'] = response.body.data['favorite']

      }
      this.pokemons[pokemonKey]['reaction'] = response.body.data['reaction']
      this.liked_pokemons = response.body.data['liked_pokemons']
      this.hated_pokemons = response.body.data['hated_pokemons']
    }
  }
}
</script>

<style scoped>
.list-group-wrapper {
  position: relative;
}
.list-group {
  overflow: auto;
  height: 50vh;
  /* border: 2px solid #dce4ec; */
  /* border-radius: 5px; */
}
.list-group-item {
  margin-top: 1px;
  border-left: none;
  border-right: none;
  border-top: none;
  /* border-bottom: 2px solid #dce4ec; */
}
.loading {
  text-align: center;
  position: absolute;
  /* color: #fff; */
  z-index: 9;
  padding: 8px 18px;
  /* border-radius: 5px; */
  left: calc(50% - 45px);
  top: calc(50% - 18px);
}

.fade-enter-active, .fade-leave-active {
  transition: opacity .5s
}
.fade-enter, .fade-leave-to {
  opacity: 0
}
</style>
