<template>
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <input class="focus:ring-indigo-500 focus:border-indigo-500 block w-full pl-7 pr-12 sm:text-sm border-gray-300 rounded-md" placeholder="enter device public key" :type="passwordFieldType" v-model="public_key" />

                <br>

                <input class="focus:ring-indigo-500 focus:border-indigo-500 block w-full pl-7 pr-12 sm:text-sm border-gray-300 rounded-md" placeholder="enter device private key" :type="passwordFieldType" v-model="private_key" />

                <br>

                <button class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded inline-flex items-center" type="password" @click="switchVisibility">show / hide</button>

                <button class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded inline-flex items-center" @click="socketConnect">Connect</button>

                <br />
                <div class="-mx-4 sm:-mx-8 px-4 sm:px-8 py-4 overflow-x-auto">
                    <div class="inline-block min-w-full shadow rounded-lg overflow-hidden">
                        <table class="min-w-full leading-normal">
                            <thead>
                                <tr>
                                    <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                        ID
                                    </th>
                                    <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                        Device ID
                                    </th>
                                    <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                        Payload
                                    </th>
                                    <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                        Created At
                                    </th>
                                </tr>
                            </thead>
                            <tbody id="device_data">
                                <tr v-for="(data, index) in socket_data" :key="data.id">
                                    <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm w-2/5">
                                        {{ index + 1 }}
                                    </td>
                                    <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm w-2/5">
                                        {{ data.device_id }}
                                    </td>
                                    <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm w-2/5">
                                        {{ data.payload }}
                                    </td>
                                    <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm w-2/5">
                                        {{ data.created_at }}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</template>

<script>
import Echo from "laravel-echo";
window.Pusher = require("pusher-js");
const Toast = Swal.mixin({
    toast: true,
    position: 'bottom-end',
    showConfirmButton: false,
    timer: 3000,
    timerProgressBar: true,
    didOpen: (toast) => {
        toast.addEventListener('mouseenter', Swal.stopTimer)
        toast.addEventListener('mouseleave', Swal.resumeTimer)
    }
})
export default {
    data() {
        return {
            socket_data: [],
            device_token: null,
            device_id: null,
            authorization_state: false,
            device_id: null,
            public_key: "",
            private_key: "",
            passwordFieldType: 'password',
        };
    },
    watch: {
        //
    },
    methods: {
        socketConnect: function () {
            this.authorize();
        },
        switchVisibility() {
            this.passwordFieldType = this.passwordFieldType === 'password' ? 'text' : 'password'
        },
        clearSocketData: function () {
            this.socket_data = [];
        },
        msg: function (type = 'success', message) {
            Swal.fire({
                position: 'top-end',
                icon: type,
                showConfirmButton: false,
                timer: 1500,
                text: message
            })
        },
        notification: function (message) {
            Toast.fire({
                type: 'success',
                title: message
            })
        },
        authorize: function () {
            axios
                .post("/iot/v1/login", {
                    public_key: this.public_key,
                    private_key: this.private_key,
                })
                .then(({
                    data
                }) => {
                    this.device_token = data.token;
                    this.device_id = data.device_id;
                    window.localECHO = new Echo({
                        broadcaster: "pusher",
                        key: process.env.MIX_PUSHER_APP_KEY,
                        cluster: process.env.MIX_PUSHER_APP_CLUSTER,
                        forceTLS: false,
                        wsHost: window.location.hostname,
                        wsPort: 6001,
                        disableStats: true,
                        authorizer: (channel, options) => {
                            console.log(options, channel);
                            return {
                                authorize: (socketId, callback) => {
                                    axios({
                                            method: "POST",
                                            url: "/api/broadcasting/auth",
                                            headers: {
                                                Authorization: `Bearer ${this.device_token}`,
                                            },
                                            data: {
                                                socket_id: socketId,
                                                channel_name: channel.name,
                                            },
                                        })
                                        .then((response) => {
                                            console.log(response);
                                            this.msg("success", "Connected")
                                            this.authorization_state = true;
                                            callback(false, response.data);
                                        })
                                        .catch((error) => {
                                            console.log(error);
                                            this.authorization_state = false;
                                            this.msg("error", "Failed To Connect")
                                            callback(true, error);
                                        });
                                },
                            };
                        },
                    });
                    localECHO.private(`App.Device.${this.device_id}`)
                        .listen(
                            ".send_data_event",
                            (e) => {
                                this.notification("device #" + this.device_id)
                                this.socket_data.push(e);
                            }
                        );
                }).catch((error) => {
                    this.msg("error", "Failed To Connect")
                });
        },
    },
    created() {
        //
    },
};
</script>
