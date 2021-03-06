<template>
    <div>
        <h1>Claim license</h1>

        <div class="card mb-6">
            <div class="card-body">
                <h3>Claim a Craft CMS license</h3>
                <p class="text-secondary">Attach a Craft CMS license to your Craft ID account.</p>

                <form class="mb-6" @submit.prevent="claimCmsLicense()">
                    <textarea-field id="cmsLicenseKey" class="mono" spellcheck="false" v-model="cmsLicenseKey" @input="cmsLicenseKeyChange" label="Craft CMS License Key" rows="5" />
                    <input type="submit" class="btn btn-primary" value="Claim License" :class="{disabled: !cmsLicenseValidates }" :disabled="!cmsLicenseValidates" />
                    <spinner v-if="cmsLicenseLoading"></spinner>
                </form>

                <form @submit.prevent="claimCmsLicenseFile()">
                    <div class="form-group mb-4">
                        <label for="licenseFile" class="block">Or upload your license.key file</label>
                        <input class="form-control" type="file" id="licenseFile" name="licenseFile" ref="licenseFile" @change="cmsLicenseFileChange" />
                    </div>

                    <input type="submit" class="btn btn-primary" value="Claim License" :class="{disabled: !cmsLicenseFileValidates }" :disabled="!cmsLicenseFileValidates" />
                    <spinner v-if="cmsLicenseFileLoading"></spinner>
                </form>
            </div>
        </div>

        <div class="card mb-6">
            <div class="card-body">
                <h3>Claim a plugin license</h3>
                <p class="text-secondary">Attach a plugin license to your Craft ID account.</p>

                <form @submit.prevent="claimPluginLicense()">
                    <text-field id="pluginLicenseKey" class="mono" spellcheck="false" v-model="pluginLicenseKey" label="Plugin License Key" placeholder="XXXX-XXXX-XXXX-XXXX-XXXX-XXXX" :mask="{ mask: '****-****-****-****-****-****', placeholder: ' ', showMaskOnHover: false, showMaskOnFocus: false }" />
                    <input type="submit" class="btn btn-primary" value="Claim License" :class="{disabled: !pluginLicenseValidates }" :disabled="!pluginLicenseValidates" />
                    <spinner v-if="pluginLicenseLoading"></spinner>
                </form>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <h3>Claim licenses by your email address</h3>
                <p class="text-secondary">Use an email address to attach Craft CMS and plugin licenses to your Craft ID account.</p>

                <form @submit.prevent="claimLicensesByEmail()">
                    <text-field id="email" label="Email Address" v-model="email" placeholder="user@example.com" />
                    <input type="submit" class="btn btn-primary" value="Claim Licenses" :class="{disabled: $v.email.$invalid }" :disabled="$v.email.$invalid" />
                    <spinner v-if="emailLoading"></spinner>
                </form>
            </div>
        </div>
    </div>
</template>


<script>
    import { required, email } from 'vuelidate/lib/validators'
    import Spinner from '../../components/Spinner'

    export default {

        components: {
            Spinner,
        },

        data() {
            return {
                cmsLicenseKey: '',
                cmsLicenseLoading: false,
                cmsLicenseValidates: false,
                cmsLicenseFile: '',
                cmsLicenseFileLoading: false,
                cmsLicenseFileValidates: false,
                pluginLicenseKey: '',
                pluginLicenseLoading: false,
                pluginLicenseValidates: false,
                email: '',
                emailLoading: false,
            }
        },

        validations: {
            email: {
                required,
                email,
            },
        },

        methods: {

            checkCmsLicense() {
                if(this.cmsLicenseKey.length === 258) {
                    return true;
                }

                return false;
            },

            checkPluginLicense() {
                const normalizedValue = this.pluginLicenseKey.replace(/(- )/gm, "").trim();

                if(normalizedValue.length === 29) {
                    return true;
                }

                return false;
            },

            claimCmsLicense() {
                this.cmsLicenseLoading = true;

                this.$store.dispatch('licenses/claimCmsLicense', this.cmsLicenseKey)
                    .then(() => {
                        this.cmsLicenseLoading = false;
                        this.$store.dispatch('licenses/getCmsLicenses');
                        this.$store.dispatch('licenses/getPluginLicenses');
                        this.$store.dispatch('account/getInvoices');
                        this.$store.dispatch('app/displayNotice', 'CMS license claimed.');
                        this.$router.push({path: '/licenses/cms'});
                    })
                    .catch(response => {
                        this.cmsLicenseLoading = false;
                        const errorMessage = response.data && response.data.error ? response.data.error : 'Couldn’t claim CMS license.';
                        this.$store.dispatch('app/displayError', errorMessage);
                    });
            },

            claimCmsLicenseFile() {
                this.$store.dispatch('licenses/claimCmsLicenseFile', this.$refs.licenseFile.files[0])
                    .then(() => {
                        this.cmsLicenseFileLoading = false;
                        this.$store.dispatch('licenses/getCmsLicenses');
                        this.$store.dispatch('licenses/getPluginLicenses');
                        this.$store.dispatch('account/getInvoices');
                        this.$store.dispatch('app/displayNotice', 'CMS license claimed.');
                        this.$router.push({path: '/licenses/cms'});
                    })
                    .catch(response => {
                        this.cmsLicenseFileLoading = false;
                        const errorMessage = response.data && response.data.error ? response.data.error : 'Couldn’t claim CMS license.';
                        this.$store.dispatch('app/displayError', errorMessage);
                    });
            },

            claimLicensesByEmail() {
                this.emailLoading = true;

                this.$store.dispatch('licenses/claimLicensesByEmail', this.email)
                    .then(() => {
                        this.emailLoading = false;
                        this.$store.dispatch('licenses/getCmsLicenses');
                        this.$store.dispatch('licenses/getPluginLicenses');
                        this.$store.dispatch('account/getInvoices');
                        this.$store.dispatch('app/displayNotice', 'Verification email sent to ' + this.email + '.');
                    })
                    .catch(response => {
                        this.emailLoading = false;
                        const errorMessage = response.data && response.data.error ? response.data.error : 'Couldn’t claim licenses.';
                        this.$store.dispatch('app/displayError', errorMessage);
                    });
            },

            claimPluginLicense() {
                this.pluginLicenseLoading = true;

                this.$store.dispatch('licenses/claimPluginLicense', this.pluginLicenseKey)
                    .then(() => {
                        this.pluginLicenseLoading = false;
                        this.$store.dispatch('licenses/getCmsLicenses');
                        this.$store.dispatch('licenses/getPluginLicenses');
                        this.$store.dispatch('account/getInvoices');
                        this.$store.dispatch('app/displayNotice', 'Plugin license claimed.');
                        this.$router.push({path: '/licenses/plugins'});
                    })
                    .catch(response => {
                        this.pluginLicenseLoading = false;
                        const errorMessage = response.data && response.data.error ? response.data.error : 'Couldn’t claim plugin license.';
                        this.$store.dispatch('app/displayError', errorMessage);
                    });
            },

            cmsLicenseKeyChange(value) {
                this.$nextTick(() => {
                    this.cmsLicenseKey = this.$options.filters.formatCmsLicense(value);
                });
            },

            cmsLicenseFileChange() {
                this.cmsLicenseFileValidates = this.$refs.licenseFile.files.length > 0;
            }

        },

        watch: {

            cmsLicenseKey() {
                this.cmsLicenseValidates = this.checkCmsLicense();
            },

            pluginLicenseKey() {
                this.pluginLicenseValidates = this.checkPluginLicense();
            }

        },

    }
</script>
