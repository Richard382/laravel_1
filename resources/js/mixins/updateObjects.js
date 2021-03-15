export default {
    data() {
        return {
            temporary_properties: []
        }
    },
    methods: {
        updateObjects() {
            let selected = this.form.property_type
            let property_type = [];
            let self = this;

            self.temporary_properties = [];
            self.form.properties = [];

            this.property_types.forEach(function (item) {
                if (selected.includes(item.id)) {
                    property_type.push(item)
                }
            })

            property_type.forEach(function (item) {
                item.properties.forEach(function (prop) {
                    self.temporary_properties.push(prop)
                })
            })
        },
    }
}
