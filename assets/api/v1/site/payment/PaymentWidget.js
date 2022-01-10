import RobokassaWidget from "../RobokassaWidget";

// adapter
export default class PaymentWidget {
    constructor(serviceApi) {
        this.widget = new RobokassaWidget(serviceApi);
    }

    init() {
        this.widget.init();
    }

    close() {
        this.widget.close();
    }
}