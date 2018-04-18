
export function buildConditionsConfig({trigger}) {
    const config = JSON.parse(trigger.val());
    config.id = trigger.data('id');
    return config;

}