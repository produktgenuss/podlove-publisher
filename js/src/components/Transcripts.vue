<template>
    <div class="container">
       
        <div class="row">

            <div class="transcripts-tab-wrapper">
                
                <a href="#" 
                   class="transcripts-tab" 
                   :class="{'transcripts-tab-active': mode === 'voices'}" 
                   @click.prevent="mode = 'voices'">
                   Voices
                </a>

                <a href="#" 
                   class="transcripts-tab" 
                   :class="{'transcripts-tab-active': mode === 'import'}" 
                   @click.prevent="mode = 'import'">
                   Import
                </a>

                <a href="#" 
                   class="transcripts-tab" 
                   :class="{'transcripts-tab-active': mode === 'export'}" 
                   @click.prevent="mode = 'export'">
                   Export
                </a>

            </div>

        </div>

        <div class="row import" v-show="mode == 'import'">
            <p>
                <form>
                    <button class="button button-primary" @click.prevent="initImportTranscript">Import Transcript</button>
                    <input type="file" name="transcriptimport" id="transcriptimport" @change="importTranscript" style="display: none" :disabled="importing"> 
                    <div class="description">{{ description }}</div>
                </form>
            </p>
        </div>  

    </div>
</template>

<script type="text/javascript">
export default {
    data() {
        return {
            mode: 'import',
            importing: false,
            lastError: ''
        }
    },

    computed: {
        description: function() {
            if (this.importing) {
                return "importing ..."
            } else if (this.lastError) {
                return this.lastError
            } else {
                return "Accepts: WebVTT"
            }
        }
    },

    methods: {
        initImportTranscript () {
            const fileInput = document.getElementById("transcriptimport");
            fileInput.click();
        },
        importTranscript() {
            const fileInput = document.getElementById("transcriptimport");
            const file = fileInput.files[0];

            this.importing = true;
            
            let form = new FormData()
            form.append('transcript', file)
            form.append('action', 'podlove_transcript_import')
            form.append('post_id', document.querySelector('#post_ID').value)

            this.axios.post(ajaxurl, form, {
                headers: {
                  'Content-Type': 'multipart/form-data'
                }
            }).then(({data}) => {
                if (data.error) {
                    this.lastError = data.error
                } else {
                    this.mode = 'voices';
                }
                this.importing = false;
                fileInput.parentElement.reset();
            }).catch((error) => {
                this.importing = false;
                fileInput.parentElement.reset();
            })
        },
        doImportTranscript(text) {
            console.log("import", text);
        }
    }
}
</script>

<style type="text/css">
.row {
    position: relative;
}
.col-md-12 {
    width: 100%;
}
.col-md-8 {
    flex-grow: 3;
}
.col-md-4 {
    flex-grow: 1;
}
.transcripts-tab-wrapper {
    width: 100%;
    height:28px;
    display: block;
}
.transcripts-tab-wrapper::after {
    clear: both;
}
.transcripts-tab {
    float: left;
    border: 1px solid #ccc;
    border-bottom: none;
    margin-right: 0.5em;
    padding: 1px 10px;
    font-size: 1em;
    line-height: 24px;
    background: #f6f6f6;
}
a.transcripts-tab, a.transcripts-tab:focus {
    text-decoration: none;
    color: #555;
    outline: none;
    box-shadow: none
}
.transcripts-tab-active,
.transcripts-tab:hover{
    background: white;
}
.import form {
    border: 2px dashed #999;
    padding: 20px 20px;
    margin-bottom: 10px;
    text-align: center;
}

.import .description {
    margin-top: 10px;
    color: #666;
}
</style>
