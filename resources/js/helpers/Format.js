const Format = (value) => {
    const Number = () => {
        var parts = value.toString().split(".");
        parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, " ");
        return parts.join(".");

    }

    return {Number}
}

export default Format
