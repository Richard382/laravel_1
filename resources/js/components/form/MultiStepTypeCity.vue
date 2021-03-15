<template>

   <div class="register__multistepper">
      <v-divider></v-divider>

      <template v-for="(path, index) in paths">
         <div
            :class="[
                    'checkbox-group',
                    path.done && 'checkbox-group--done',
                    path_errors.includes(index) && 'checkbox-group--error'
                ]"
         >
            <div class="checkbox-group__element">

               <template v-if="!path.done">

                  <ValidationProvider
                     rules="required"
                     v-slot="{ errors }"
                     :ref="uniqueSlug(1, index)"
                     :name="'paths.' + index + '.property_type'"
                  >
                     <template v-if="path.step === 1">
                        <div :class="['checkbox-group__label', errors[0] ?  'checkbox-group__label--error' : '']">
                           Pasirinkite jūsų teikiamų paslaugų segmentą, objektą, teikiamas paslaugas, vietoves ir kainas
                        </div>
                        <div class="checkbox-group__sub-label">Galite rinktis tik vieną segmentą</div>
                        <div class="checkbox-group__sub-label">
                                    <span
                                       v-for="step in steps"
                                       :class="['stepper', step.step === path.step ? 'current' : '']"
                                       @click="handleBreadcrumbClick(step, path,index)"
                                    >
                                        <span>{{ step.label }}</span> {{ steps.length !== step.step ? ' >' : '' }}
                                    </span>
                        </div>
                        <template v-for="(property_type, propertyTypeIndex) in property_types">
                           <v-radio-group
                              v-model="path.data.property_type"
                              :mandatory="false"
                              class="v-input--radio-group-custom"
                           >
                              <v-radio
                                 :label="property_type.name"
                                 :value="property_type.id"
                                 hide-details
                                 :error-messages="errors"
                                 :ripple="false"
                                 @change="propertyChanged(path, property_type); next(path, index, true)"
                              ></v-radio>
                           </v-radio-group>
                        </template>
                     </template>
                  </ValidationProvider>


                  <template v-if="path.step === 2">
                     <ValidationProvider
                        rules="required"
                        v-slot="{ errors }"
                        :ref="uniqueSlug(2, index)"
                        :key="Date.now()"
                        :name="'paths.' + index + '.properties.' + Date.now()"
                     >
                        <div :class="['checkbox-group__label', errors[0] ?  'checkbox-group__label--error' : '']">
                           Pasirinkite jūsų teikiamų paslaugų segmentą, objektą, teikiamas paslaugas, vietoves ir kainas
                        </div>
                        <div class="checkbox-group__sub-label">Galite rinktis tik vieną objektą</div>
                        <div class="checkbox-group__sub-label">
                                    <span
                                       v-for="step in steps"
                                       :class="['stepper', step.step === path.step ? 'current' : '']"
                                       @click="handleBreadcrumbClick(step, path, index)"
                                    >
                                        <span>{{ step.label }}</span> {{ steps.length !== step.step ? ' >' : '' }}
                                    </span>
                        </div>
                        <template v-for="property in path.temporary_properties">
                           <v-radio-group
                              v-model="path.data.properties"
                              :mandatory="false"
                              class="v-input--radio-group-custom"
                           >
                               
                              <v-radio
                                 :key="property.name"
                                 :label="property.name"
                                 :value="property.id"
                                 hide-details
                                 :error-messages="errors"
                                 :ripple="false"
                                 @change="propertyObjectChanged(path, property); next(path, index, true)"
                              ></v-radio>
                           </v-radio-group>
                        </template>
                     </ValidationProvider>
                  </template>


                  <ValidationProvider
                     rules="required"
                     v-slot="{ errors }"
                     :ref="uniqueSlug(3, index)"
                     :name="'paths.' + index + '.services'"
                  >
                     <template v-if="path.step === 3">
                        <div :class="['checkbox-group__label', errors[0] ?  'checkbox-group__label--error' : '']">
                           Pasirinkite jūsų teikiamų paslaugų segmentą, objektą, teikiamas paslaugas, vietoves ir kainas
                        </div>
                        <div class="checkbox-group__sub-label">Galite rinktis kelias paslaugas</div>
                        <div class="checkbox-group__sub-label">
                                    <span
                                       v-for="step in steps"
                                       :class="['stepper', step.step === path.step ? 'current' : '']"
                                       @click="handleBreadcrumbClick(step, path,index)"
                                    >
                                        <span>{{ step.label }}</span> {{ steps.length !== step.step ? ' >' : '' }}
                                    </span>
                        </div>
                        <v-checkbox
                           label="Visi"
                           hide-details
                           on-icon=""
                           :value="false"
                           off-icon=""
                           class="v-input--custom-checkbox all-visicheck"
                           :ripple="false"
                           @click="checkAllServices(path);nextIfAllCheck(path, index, true)"
                        ></v-checkbox>
                        <!--next(path, index, true);-->
                        <template v-for="(service, index2) in path.services">
                           <v-checkbox
                              v-model="path.data.services"
                              :label="service.name"
                              :value="service.id"
                              hide-details
                              :error-messages="errors"
                              on-icon=""
                              off-icon=""
                              multiple
                              class="v-input--custom-checkbox"
                              :ripple="false"
                              @click="allServiceEffect(path,index)"
                           ></v-checkbox>
                        </template>
                     </template>
                  </ValidationProvider>

                  <ValidationProvider
                     rules="required"
                     v-slot="{ errors }"
                     :ref="uniqueSlug(4, index)"
                     :name="'paths.' + index + '.regions'"
                  >
                     <template v-if="path.step === 4">
                        <div :class="['checkbox-group__label', errors[0] ?  'checkbox-group__label--error' : '']">
                           Pasirinkite jūsų teikiamų paslaugų segmentą, objektą, teikiamas paslaugas, vietoves ir kainas
                        </div>
                        <div class="checkbox-group__sub-label">Galite rinktis kelias vietoves</div>
                        <div class="checkbox-group__sub-label">
                                    <span
                                       v-for="step in steps"
                                       :class="['stepper', step.step === path.step ? 'current' : '']"
                                       @click="handleBreadcrumbClick(step, path, index)"
                                    >
                                        <span>{{ step.label }}</span> {{ steps.length !== step.step ? ' >' : '' }}
                                    </span>
                        </div>
                        <v-select
                           v-model="path.data.regions"
                           :items="regions"
                           label="Vietovės"
                           item-text="name"
                           item-value="id"
                           dense
                           chips
                           multiple
                           no-data-text="Nėra tokios vietovės"
                           class="v-input--labeled"
                           placeholder="Pasirinkite"
                           :error-messages="errors"
                           :menu-props="{bottom: true, offsetY: true, maxHeight: 240}"
                        >
                           <template v-slot:prepend-item>
                              <v-list-item
                                 ripple
                                 @click="toggleCities(path.data,path, index, true);"
                              >
                                 <v-list-item-action>
                                    <v-icon :color="path.data.regions.length > 0 ? 'indigo darken-4' : ''">{{
                                       icon(path.data) }}
                                    </v-icon>
                                 </v-list-item-action>
                                 <v-list-item-content>
                                    <v-list-item-title>Visa Lietuva</v-list-item-title>
                                 </v-list-item-content>
                              </v-list-item>
                           </template>
                           <template v-slot:item="data">
                              <v-list-item-action>
                                 <v-checkbox
                                    :input-value="data.attrs.inputValue"
                                    color="primary"
                                 ></v-checkbox>
                              </v-list-item-action>
                              <v-list-item-content>
                                 <v-list-item-title v-html="data.item.name"></v-list-item-title>
                              </v-list-item-content>
                           </template>
                           <template v-slot:selection="{ item, index }">
                              <v-chip v-if="index < 1">
                                 <span>{{ item.name }}</span>
                              </v-chip>
                              <span
                                 v-if="index === 1"
                                 class="black--text caption"
                              >(+{{ path.data.regions.length - 1 }} kiti)</span>
                           </template>
                        </v-select>
                     </template>
                  </ValidationProvider>

                  <template v-if="path.step === 5">
                     <ValidationProvider
                        v-slot="{ errors }"
                        :ref="uniqueSlug(5, index)"
                        :name="'paths.' + index + '.price_from'"
                     >
                        <div :class="['checkbox-group__label', errors[0] ?  'checkbox-group__label--error' : '']">
                           Pasirinkite jūsų teikiamų paslaugų segmentą, objektą, teikiamas paslaugas, vietoves ir kainas
                        </div>
                        <div class="checkbox-group__sub-label">Kokiame turto intervale dirbate?</div>
                        <div class="checkbox-group__sub-label">
                                    <span
                                       v-for="step in steps"
                                       :class="['stepper', step.step === path.step ? 'current' : '']"
                                       @click="handleBreadcrumbClick(step, path, index)"
                                    >
                                        <span>{{ step.label }}</span> {{ steps.length !== step.step ? ' >' : '' }}
                                    </span>
                        </div>
                        <number-field
                           v-model="path.data.price_from"
                           label="Paslaugos Kaina Nuo"
                           placeholder="pvz: nuo €100"
                           :errors="errors"
                        />
                     </ValidationProvider>

                     <ValidationProvider
                        :rules="{'min_value': (path.data.price_from ? (parseInt(path.data.price_from)+1) : 1)}"
                        v-slot="{ errors }"
                        :ref="uniqueSlug(5, index) + '-price_to'"
                        :name="'paths.' + index + '.price_to'"
                     >
                        <number-field
                           v-model="path.data.price_to"
                           label="Paslaugos Kaina Iki"
                           placeholder="pvz: iki €600"
                           :errors="errors"
                        />
                     </ValidationProvider>
                  </template>

                  <div class="checkbox-group__bottom d-flex align-center">
                     <div class="checkbox-group__steps mr-auto">
                        {{ path.step }}/{{ max_steps }}
                     </div>
                     <div class="checkbox-group__next">
                        <v-btn
                           color="red"
                           small
                           v-if="index !== 0"
                           @click="removeItem(index)"
                           class="v-btn--nominwidth"
                        >
                           <v-icon>mdi-close</v-icon>
                        </v-btn>
                        <v-btn
                           v-if="path.step > 1"
                           text
                           :ripple="false"
                           class="mr-4 text-capitalize"
                           style="text-decoration: none !important; font-size: 12px"
                           @click="decreaseStep(path)"
                        >
                           Grįžti
                        </v-btn>
                        <v-btn
                           color="primary"
                           small
                           @click="next(path, index)"
                        >
                           {{ path.step === max_steps ? 'Baigti' : 'Tęsti' }}
                        </v-btn>
                         <!--v-if="path.step !== 1 && path.step !== 2"-->
                     </div>
                      
                  </div>

               </template>

               <template v-else>
                  <div class="checkbox-group__label">Objekto tipas, objektas, lokacija ir kaina:</div>
                  <p class="checkbox-group__info">{{ getTypeName(path) }} - {{ getObejctsName(path) }} - {{
                     getServicesName(path) }} - {{ getCitiesName(path) }} - €{{ path.data.price_from + '-' +
                     path.data.price_to }}</p>
                  <div class="checkbox-group__next d-flex justify-end">
                     <v-btn
                        text
                        @click="editPath(path)"
                     >
                        Redaguoti
                     </v-btn>
                  </div>
               </template>
            </div>
         </div>
      </template>

      <v-btn
         color="primary"
         width="100%"
         @click="addPath"
      >
         <v-icon left>mdi-plus</v-icon>
         Pridėti naują paslaugą
      </v-btn>

      <v-divider></v-divider>
   </div>

</template>

<script>
   import {ValidationProvider} from "vee-validate";
   import Cloner from "../../helpers/Cloner";
   import Format from "../../helpers/Format";
   import NumberField from "./NumberField";

   export default {
      components: {
         NumberField,
         ValidationProvider
      },
      name: "MultiStepTypeCity",
      data() {
         return {
            group_increment: 0,
            max_steps: 5,
            steps: [
               {
                  step: 1,
                  label: 'Segmentas'
               },
               {
                  step: 2,
                  label: 'Objektas',
               },
               {
                  step: 3,
                  label: 'Paslaugos',
               },
               {
                  step: 4,
                  label: 'Vietos',
               },
               {
                  step: 5,
                  label: 'Kaina',
               }
            ],
            paths: [{
               // data: {"property_type":4,"properties":[31],"cities":[2,5],"price":"nuo 700"},
               // done: true,
               // step: 5,
               // temporary_properties: [{"id":29,"property_type_id":4,"name":"Prekybos centras","created_at":"2019-11-19 20:01:42","updated_at":"2019-11-19 20:01:42"},{"id":30,"property_type_id":4,"name":"Parduotuvė","created_at":"2019-11-19 20:01:42","updated_at":"2019-11-19 20:01:42"},{"id":31,"property_type_id":4,"name":"Butikas","created_at":"2019-11-19 20:01:42","updated_at":"2019-11-19 20:01:42"},{"id":32,"property_type_id":4,"name":"Vaistinės","created_at":"2019-11-19 20:01:42","updated_at":"2019-11-19 20:01:42"},{"id":33,"property_type_id":4,"name":"Degalinės","created_at":"2019-11-19 20:01:42","updated_at":"2019-11-19 20:01:42"},{"id":34,"property_type_id":4,"name":"Turgus","created_at":"2019-11-19 20:01:42","updated_at":"2019-11-19 20:01:42"},{"id":35,"property_type_id":4,"name":"Kita","created_at":"2019-11-19 20:01:42","updated_at":"2019-11-19 20:01:42"}],
               data: {
                  property_type: '',
                  properties: '',
                  regions: '',
                  price_from: '',
                  price_to: '',
                  services: [],
               },
               all_services: false,
               checkBoxAllServices:0,
               done: false,
               step: 1,
               prev_step: 1,
               temporary_objects: [],
            }],
            clean: null,
            allservicemodel: [1]
         }
      },
      props: {
         value: {
            type: Array,
            default() {
               return [];
            }
         },
         property_types: {
            type: Array,
         },
//         services: {
//            type: Array,
//         },
         regions: {
            type: Array,
         },
         path_errors: {
            type: Array,
         },
         preset: {
            type: [Boolean, Array],
            default() {
               return false;
            }
         }
      },
      methods: {

         checkAllServices(path) {
            path.data.services = []

            if (! path.all_services) {
               path.services.forEach(function (service) {
                  if (!path.data.services.includes(service.id)) {
                     path.data.services.push(service.id)
                  }
               })

               path.all_services = true;
//               document.querySelector(".all-visicheck input").checked = true;

            } else {

               path.all_services = false;
//               document.querySelector(".all-visicheck input").checked = false;
//               console.log('in false')
            }
            setTimeout(() => {
//                let evt = document.createEvent("HTMLEvents");
//                evt.initEvent("change", false, true);
//                document.querySelector(".all-visicheck input").dispatchEvent(evt);
            },300)
            
         },
         nextIfAllCheck(path, index, dd){
             if (path.all_services) {
                 this.next(path, index, dd);
             }
         },
         allServiceEffect(path,index){
             console.log()
             if(path.all_services){
                let evt = document.createEvent("HTMLEvents");
                   evt.initEvent("change", false, true);
//                document.querySelector(".all-visicheck input").checked = false;

   //             $(".all-visicheck input").prop('checked',false);
                path.all_services = false;
                document.querySelectorAll(".register__multistepper .checkbox-group")[index].querySelector('.all-visicheck input')
                    .dispatchEvent(evt);
            }
         },
         propertyChanged(path, property_type) {
            console.log('property_type',property_type)
            path.temporary_properties = property_type.properties;
            path.data.properties = null;
         },
         propertyObjectChanged(path, property) {
            console.log('property',property);
            path.services = property.services;
            path.data.services = null;
         },

         selectedAllCities(data) {
            return data.regions.length === (this.regions.length - 1)
         },

         selectedSomeCities(data) {
            return data.regions.length > 0 && !this.selectedAllCities(data)
         },

         icon(data) {
            if (this.selectedAllCities(data)) return 'mdi-close-box'
            if (this.selectedSomeCities(data)) return 'mdi-minus-box'
            return 'mdi-checkbox-blank-outline'
         },

         toggleCities(data, path, index, a) {
             
             
            this.$nextTick(() => {
               if (this.selectedAllCities(data)) {
                  data.regions = ''
               } else {
                  data.regions = [];

                  this.regions.forEach(region => {
                     if (!region.divider) {
                        data.regions.push(region.id)
                     }
                  });
                  //data.cities = this.cities.slice()
               }
                if(data.regions !== ""){
                    console.log("cities data",data.regions,data)
                    this.next(path, index, a)
                }
            })
            
         },

         removeItem(index) {
            this.paths.splice(index, 1);
            this.savePaths()
         },

         getCitiesName(path) {
            let values = [];

            this.regions.forEach(function (region) {
               path.data.regions.forEach(function (pregion) {
                  if (pregion === region.id) {
                     values.push(region.name)
                  }
               })
            })

            return values.join(', ');
         },
         
        async handleBreadcrumbClick(step, path, index = false) {
//             console.log('index', index);
            let stepIncreased = false;
            if(index === false || step.step < path.step) {
                if (step.step <= path.prev_step) {
                   path.step = step.step
                }
            }else{
                let stepd = await this.$refs['group-' + path.step + '-' + index][0].validate();
                if (stepd.valid) {
                   this.increaseStep(path,index);
                   path.step = step.step
                   stepIncreased = true;
                }
            }
            if(path.step == 3 && !stepIncreased){
                this.setServicesAllCheck(path,index);
            }
        },

        getTypeName(path) {
            let value;

            this.property_types.forEach(function (property_type) {
               if (path.data.property_type === property_type.id) {
                  value = property_type.name;
                  return;
               }
            });

            return value;
        },

         getServicesName(path) {
            let values = [];

//            this.property_types.forEach(function (property_type) {
//                if(property_type.properties){
//                    property_type.properties.forEach(function (property) {
//                        if(property.services){
//                            property.services.forEach(function (service) {
//                                path.data.services.forEach(function (pservice) {
//                                    if (pservice === service.id) {
//                                       values.push(service.name)
//                                    }
//                                 })
//                            });
//                        }
//                    });
//                }
//            });
//            if(this.services){
                path.services.forEach(function (service) {
                    path.data.services.forEach(function (pservice) {
                      if (pservice === service.id) {
                         values.push(service.name)
                      }
                   })
                })
//            }

            return values.join(', ');
         },

         getObejctsName(path) {
            let values = [];

            path.temporary_properties.forEach(function (property) {
               if (path.data.properties === property.id) {
                  values.push(property.name)
               }
               // path.data.properties.forEach(function (pproperty) {
               //   if (pproperty === property.id) {
               //     values.push(property.name)
               //   }
               // })
            })

            return values.join(', ');
         },

         getCleanPathsData() {
            let data = [];

            this.paths.forEach(function (path) {
               data.push(path.data)
            });

            return data;
         },

         uniqueSlug(number, index) {
            return 'group-' + number + '-' + index;
         },

         increaseStep(path, index = 0) {
            if (path.step === this.max_steps) {
               path.done = true;
               this.savePaths();
               return;
            } else {
               this.savePaths();
            }

            path.step++;

            if (path.step > path.prev_step) {
               path.prev_step = path.step
            }
            if(path.step == 3){
                this.setServicesAllCheck(path,index);
            }
         },

         decreaseStep(path) {
            if (path.step <= 1) {
               return
            }

            path.step--;
         },

         async next(path, index, fastForward = false) {
            let self = this;

            if (fastForward) {
               setTimeout(function () {
                  self.increaseStep(path,index);
               }, 300)

               return;
            }

            let step = await this.$refs['group-' + path.step + '-' + index][0].validate();
            let add_step = {valid: true};

            if (path.step === 5) {
               add_step = await this.$refs['group-' + path.step + '-' + index + '-price_to'][0].validate();
            }

            if (step.valid && add_step.valid) {
               this.increaseStep(path,index);
            }
            if(path.step == 3){
                this.setServicesAllCheck(path,index);
            }
         },
         setServicesAllCheck(path,index){
             setTimeout(function(){
                if(path.all_services){
                    let evnt= document.createEvent("HTMLEvents");
                    evnt.initEvent("change");
                    console.log("index",index);
                        document.querySelectorAll(".register__multistepper .checkbox-group")[index].querySelector('.all-visicheck input')
                        .dispatchEvent(evnt);
                }                
            },100);
         },
         editPath(path) {
            path.step = 1;
            path.done = false;
         },

         addPath() {
            this.paths.push({
               data: {
                  property_type: '',
                  properties: '',
                  regions: '',
                  price_from: '',
                  price_to: '',
                  services: '',
               },
               all_services:false,
               done: false,
               step: 1,
               prev_step: 1,
               temporary_objects: [],
            })
         },
         clearPaths() {
            this.paths = Object.assign([], this.clean);
         },
         savePaths() {
            this.$emit('input', this.getCleanPathsData());
         },

      },
      created() {
         if (this.preset) {
            this.paths = this.preset
         }

         this.clean = Cloner(this.paths)

      }
   }
</script>
