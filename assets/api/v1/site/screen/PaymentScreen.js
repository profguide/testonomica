import React from "react";
import PaymentWidget from "../payment/PaymentWidget";

export default class PaymentScreen extends React.Component {
    constructor(props) {
        super(props);
        this.widget = new PaymentWidget(props.api);
        this.widget.init();
    }

    render() {
        return null;
    }
}