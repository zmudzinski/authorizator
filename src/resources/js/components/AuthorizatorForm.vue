<template>
    <div class="authorizator">

        <div class="overlay" v-if="isProcessing">
            <div class="d-flex justify-content-center">
                <div class="spinner-border text-primary" role="status">
                    <span class="sr-only">Loading...</span>
                </div>
            </div>
        </div>

        <div v-if="errorMessage" class="alert alert-danger" role="alert">
            {{errorMessage}}
        </div>

        <div v-if="pages.selectChannel && !isProcessing">
            <p class="card-text">
                <slot></slot>
            </p>
            <div class="form-group">

                <div class="form-check" v-for="item in allowedChannels" :key=item.id>
                    <input v-model="channel" class="form-check-input" type="radio" :value=item.name :id="'radio_'+item.id">
                    <label class="form-check-label" :for="'radio_'+item.id">
                        {{ item.description }}
                    </label>
                </div>

            </div>
            <button :disabled="buttons.sendCode" class="btn btn-primary btn-success" @click="sendCode">{{ labels.sendCode }}</button>
        </div>

        <div v-if="pages.codeInput && !isProcessing">
            <div class="form-group">
                <label for="authCode">{{ labels.enterCode }}</label>
                <input v-model="code" type="text" class="form-control" id="authCode">
            </div>

            <button :disabled="buttons.verify" class="btn btn-primary btn-success" @click="verifyCode">{{ labels.confirmCode }}</button>
            <button :disabled="!buttons.resend" class="btn btn-primary" @click="sendCode">{{ resendButtonLabel }}</button>
        </div>

        <div v-if="pages.success">
            <div class="alert alert-success" role="alert">
                {{ labels.verificationSuccess }}
            </div>
            <button class="btn btn-primary btn-success" @click="goToURL">{{ labels.verificationSuccessButton }}</button>
        </div>

    </div>
</template>

<script>
    export default {
        props: {
            allowedChannels: {
                type: Array,
                required: true
            },
            countdownValue: {
                type: Number,
                default: 5
            },
            sendEndpoint: {
                type: String,
                default: '/authorization/send',
            },
            verifyEndpoint: {
                type: String,
                default: '/authorization/check',
            },
            labels: {
                default: function () {
                    return {
                        sendCode: 'Send code',
                        enterCode: 'Enter received code',
                        confirmCode: 'Confirm',
                        verificationSuccess: 'Successfully verified',
                        verificationSuccessButton: 'Ok',
                        resendButtonDefault: 'Resend code',
                        resendButtonCounting: 'Resend available in',
                    }
                }
            }
        },
        data: function () {
            return {
                resendButtonLabel: this.labels.resendButtonDefault,
                isProcessing: false,
                countdown: this.countdownValue,
                chooseChannel: true,
                errorMessage: '',
                channel: '',
                code: '',
                finalURL: '',
                buttons: {
                    resend: false,
                    sendCode: true,
                    verify: true,
                },
                pages: {
                    selectChannel: true,
                    codeInput: false,
                    success: false,
                }
            }
        },
        methods: {
            sendCode: function () {
                this.errorMessage = '';
                this.buttons.resend = false;
                this.isProcessing = true;
                let responseData = null;
                axios.post(this.sendEndpoint, {
                    channel: this.channel,
                })
                    .then(response => {
                        responseData = response.data;
                    })
                    .catch(error => {
                        console.error(error);
                    })
                    .finally(() => {
                        if (responseData.status === 'ok') {
                            this.pages.selectChannel = false;
                            this.pages.codeInput = true;
                            this.errorMessage = '';
                            this.countdownTimer();
                        } else {
                            this.errorMessage = responseData.message;
                        }
                        this.isProcessing = false;
                    });
            },
            verifyCode: function () {
                this.errorMessage = '';
                this.isProcessing = true;
                let responseData = null;
                axios.post(this.verifyEndpoint, {
                    code: this.code,
                })
                    .then(response => {
                        responseData = response.data;
                    })
                    .catch(error => {
                        console.error(error);
                    })
                    .finally(() => {
                        if (responseData.status === 'valid') {
                            this.finalURL = responseData.url;
                            this.isProcessing = false;
                            this.pages.codeInput = false;
                            this.pages.success = true;
                            return;
                        }
                        this.errorMessage = responseData.message;
                        this.isProcessing = false;
                    });
            },
            goToURL: function () {
                window.location.href = this.finalURL;
            },
            countdownTimer: function () {
                if (this.countdown > 0) {
                    this.resendLabel(true);
                    setTimeout(() => {
                        this.countdown -= 1;
                        this.countdownTimer();
                    }, 1000)
                }
                if (this.countdown === 0) {
                    this.countdown = this.countdownValue;
                    this.buttons.resend = true;
                    this.resendLabel(false);
                }
            },
            resendLabel: function (wait) {
                this.resendButtonLabel = wait ?
                    `${this.labels.resendButtonCounting} ${this.countdown}s`
                    : this.labels.resendButtonDefault;
            }
        },
        watch: {
            channel: function () {
                this.buttons.sendCode = false;
            },
            code: function () {
                if (this.code) {
                    this.buttons.verify = false;
                }
            }
        },
    }
</script>

<style scoped>

</style>
