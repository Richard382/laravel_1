import Toastify from 'toastify-js'

export const Toaster = (type, data) => {

    const getMessages = () => {
        let message = data instanceof Object ? (data.hasOwnProperty('errors') ? data.errors : data.message) : data

        if (message instanceof Object) {
            let output = 'Formoje aptiktos klaidos:';

            for (let [field, error] of Object.entries(message)) {
                output += '<br>' + error
            }

            return output
        }

        return message
    }

    Toastify({
        text: getMessages(),
        duration: -1,
        className: `toastify--${type}`,
        gravity: "top", // `top` or `bottom`
        position: 'center', // `left`, `center` or `right`
        stopOnFocus: true, // Prevents dismissing of toast on hover
        close: true,
        offset: {
            x: 0, // horizontal axis - can be a number or a string indicating unity. eg: '2em'
            y: 70 // vertical axis - can be a number or a string indicating unity. eg: '2em'
        },
    }).showToast()
}
